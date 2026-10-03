<?php
require_once 'Flux/LogFile.php';
require_once 'Flux/Config.php';
require_once 'Flux/PayPalRestClient.php';

/**
 * Handles PayPal REST API / Webhooks payment notifications.
 *
 * This is the modern counterpart to Flux_PaymentNotifyRequest (classic
 * IPN). Both coexist -- which one is used is selected by the 'PayPalMode'
 * configuration key.
 */
class Flux_PaymentNotifyRequestRest {
	/**
	 * Logger class for logging to the PayPal REST log stored on disk.
	 *
	 * @access private
	 * @var Flux_LogFile
	 */
	private $ppLogFile;

	/**
	 * Set to true after the webhook has been verified by PayPal.
	 *
	 * @access private
	 * @var bool
	 */
	private $txnIsValid = false;

	/**
	 * Raw JSON body received with the webhook request.
	 *
	 * @access private
	 * @var string
	 */
	private $rawBody;

	/**
	 * HTTP headers received with the webhook request.
	 *
	 * @access private
	 * @var array
	 */
	private $headers;

	/**
	 * Decoded webhook event body.
	 *
	 * @access private
	 * @var array
	 */
	private $event;

	/**
	 * REST API client.
	 *
	 * @access private
	 * @var Flux_PayPalRestClient
	 */
	private $restClient;

	/**
	 * Your currently configured currency code.
	 *
	 * @access public
	 * @var string
	 */
	public $myCurrencyCode;

	/**
	 * Transactions log table.
	 *
	 * @access public
	 * @var string
	 */
	public $txnLogTable;

	/**
	 * Account credit balance table.
	 *
	 * @access public
	 * @var string
	 */
	public $creditsTable;

	/**
	 * Construct new PaymentNotifyRequestRest instance from the raw webhook
	 * JSON body and HTTP headers.
	 *
	 * @param string $rawBody
	 * @param array $headers
	 * @access public
	 */
	public function __construct($rawBody, array $headers)
	{
		$this->ppLogFile      = new Flux_LogFile(FLUX_DATA_DIR.'/logs/paypal_rest.log');
		$this->rawBody        = $rawBody;
		$this->headers        = $headers;
		$this->restClient     = new Flux_PayPalRestClient();
		$this->myCurrencyCode = strtoupper(Flux::config('DonationCurrency'));
		$this->txnLogTable    = Flux::config('FluxTables.TransactionTable');
		$this->creditsTable   = Flux::config('FluxTables.CreditsTable');
	}

	/**
	 * Log to PayPal REST log file. Works like printf().
	 *
	 * @param string $format
	 * @param mixed ...
	 * @return string
	 * @access protected
	 */
	protected function logPayPal()
	{
		$args = func_get_args();
		$func = array($this->ppLogFile, 'puts');
		return call_user_func_array($func, $args);
	}

	/**
	 * Process the webhook notification.
	 *
	 * @return bool
	 * @access public
	 */
	public function process()
	{
		$this->event = json_decode($this->rawBody, true);

		if (!is_array($this->event)) {
			$this->logPayPal('Received webhook body is not valid JSON, aborting.');
			return false;
		}

		$eventType = isset($this->event['event_type']) ? $this->event['event_type'] : '(unknown)';
		$this->logPayPal('Received webhook event: %s', $eventType);

		if (!$this->restClient->verifyWebhookSignature($this->headers, $this->rawBody)) {
			$this->logPayPal('Webhook signature verification failed, aborting.');
			return false;
		}

		$this->txnIsValid = true;
		$this->logPayPal('Webhook signature verified, proceeding.');

		// Only act on completed captures. Refund/reversal handling
		// (PAYMENT.CAPTURE.REFUNDED / PAYMENT.CAPTURE.REVERSED -> ban via
		// permanentlyBan(), mirroring classic's BanPaymentStatuses) could be
		// wired up here in a future pass, but is intentionally out of scope
		// for now.
		if ($eventType != 'PAYMENT.CAPTURE.COMPLETED') {
			$this->logPayPal('Event type "%s" is not handled, no action taken.', $eventType);
			return false;
		}

		$resource = isset($this->event['resource']) ? $this->event['resource'] : array();

		$transactionID = isset($resource['id']) ? $resource['id'] : null;
		$customID      = isset($resource['custom_id']) ? $resource['custom_id'] : null;
		$amount        = isset($resource['amount']['value']) ? (float)$resource['amount']['value'] : 0.0;
		$currencyCode  = isset($resource['amount']['currency_code']) ? strtoupper($resource['amount']['currency_code']) : '';

		// PayPal sends the capture's timestamp as ISO 8601 (e.g.
		// "2022-08-23T18:29:50Z"); convert it to the MySQL DATETIME format
		// the transactions table expects, falling back to null (rather than
		// the current time) if it's absent or unparseable.
		$captureTime  = isset($resource['create_time']) ? $resource['create_time'] : null;
		$paymentDate  = $captureTime ? gmdate('Y-m-d H:i:s', strtotime($captureTime)) : null;

		// The PAYMENT.CAPTURE.COMPLETED payload doesn't include the payer's
		// e-mail address directly -- only the order it came from does. Only
		// bother looking it up if the hold-untrusted-account feature is
		// actually enabled, since it's the only thing that needs it.
		$payerEmail = null;
		if (+(int)Flux::config('HoldUntrustedAccount')) {
			$orderID = isset($resource['supplementary_data']['related_ids']['order_id'])
				? $resource['supplementary_data']['related_ids']['order_id']
				: null;

			if ($orderID) {
				$order = $this->restClient->getOrder($orderID);
				if ($order && isset($order['payer']['email_address'])) {
					$payerEmail = $order['payer']['email_address'];
				}
				else {
					$this->logPayPal('Could not determine payer e-mail for order %s, will be treated as untrusted.', $orderID);
				}
			}
		}

		$this->logPayPal('Transaction identified as %s.', $transactionID);

		$customArray = @unserialize(base64_decode((string)$customID));
		$customArray = $customArray && is_array($customArray) ? $customArray : array();
		$customData  = new Flux_Config($customArray);
		$accountID   = $customData->get('account_id');
		$serverName  = $customData->get('server_name');

		$this->logPayPal('Game server name: %s, account ID: %s',
			($serverName ? $serverName : '(absent)'), ($accountID ? $accountID : '(absent)'));

		if (!$accountID || !$serverName) {
			$this->logPayPal('Account ID and/or game server name absent, cannot exchange for credits.');
			return false;
		}

		$servGroup = Flux::getServerGroupByName($serverName);
		if (!$servGroup) {
			$this->logPayPal('Unknown game server "%s", cannot process donation for credits.', $serverName);
			return false;
		}

		$sql = "SELECT COUNT(id) AS txn_count FROM {$servGroup->loginDatabase}.{$this->txnLogTable} WHERE txn_id = ? AND payment_status = 'Completed'";
		$sth = $servGroup->connection->getStatement($sql);
		$sth->execute(array($transactionID));

		if ($sth->fetch()->txn_count > 0) {
			$this->logPayPal('Transaction #%s was already processed, skipping duplicate crediting.', $transactionID);
			return false;
		}

		if ($currencyCode != $this->myCurrencyCode) {
			$this->logPayPal('Transaction currency not exchangeable, accepting anyways. (recv: %s, expected: %s)',
				$currencyCode, $this->myCurrencyCode);

			$exchangeableCurrency = false;
		}
		else {
			$exchangeableCurrency = true;
		}

		$this->logPayPal('Received %s (%s).', $amount, $currencyCode);
		$this->logPayPal('Payment for txn_id#%s has been completed.', $transactionID);

		$credits = 0;
		$trusted = true;

		if ($exchangeableCurrency) {
			$sql = "SELECT COUNT(account_id) AS acc_id_count FROM {$servGroup->loginDatabase}.login WHERE sex != 'S' AND group_id >= 0 AND account_id = ?";
			$sth = $servGroup->connection->getStatement($sql);
			$sth->execute(array($accountID));
			$res = $sth->fetch();

			if (!$res) {
				$this->logPayPal('Unknown account #%s on server %s, cannot exchange for credits.', $accountID, $serverName);
			}
			else {
				if (!$servGroup->loginServer->hasCreditsRecord($accountID)) {
					$this->logPayPal('Identified as first-time donation to the server from this account.');
				}

				$minimum = (float)Flux::config('MinDonationAmount');

				if ($amount >= $minimum) {
					list($trusted, $credits) = $this->depositDonationCredits($servGroup, $accountID, $amount, $payerEmail);
				}
				else {
					$this->logPayPal('User has donated less than the configured minimum, not exchanging credits.');
				}
			}
		}

		$this->logToPayPalTable($servGroup, $accountID, $serverName, $trusted, $credits, $transactionID, $amount, $currencyCode, $paymentDate, $payerEmail);

		$this->logPayPal('Done processing %s.', $transactionID);

		return true;
	}

	/**
	 * Shared crediting logic, mirroring the trust-check and deposit flow in
	 * Flux_PaymentNotifyRequest::process(). Kept as a small private helper
	 * duplicated from the classic class rather than refactoring it, to keep
	 * this change additive.
	 *
	 * @param Flux_LoginAthenaGroup $servGroup
	 * @param string $accountID
	 * @param float $amount
	 * @param string|null $payerEmail
	 * @return array array($trusted, $credits)
	 * @access private
	 */
	private function depositDonationCredits(Flux_LoginAthenaGroup $servGroup, $accountID, $amount, $payerEmail)
	{
		$trusted    = true;
		$trustTable = Flux::config('FluxTables.DonationTrustTable');
		$holdHours  = +(int)Flux::config('HoldUntrustedAccount');

		if ($holdHours && $payerEmail) {
			$sql = "SELECT account_id, email FROM {$servGroup->loginDatabase}.$trustTable WHERE account_id = ? AND email = ? LIMIT 1";
			$sth = $servGroup->connection->getStatement($sql);
			$sth->execute(array($accountID, $payerEmail));
			$res = $sth->fetch();

			if ($res && $res->account_id) {
				$this->logPayPal('Account ID and e-mail are trusted.');
				$trusted = true;
			}
			else {
				$trusted = false;
			}
		}

		$rate    = Flux::config('CreditExchangeRate');
		$credits = floor($amount / $rate);

		if ($trusted) {
			$sql = "SELECT * FROM {$servGroup->loginDatabase}.{$this->creditsTable} WHERE account_id = ?";
			$sth = $servGroup->connection->getStatement($sql);
			$sth->execute(array($accountID));
			$acc = $sth->fetch();

			$this->logPayPal('Updating account credit balance from %s to %s', (int)$acc->balance, $acc->balance + $credits);
			$res = $servGroup->loginServer->depositCredits($accountID, $credits, $amount);

			if ($res) {
				$this->logPayPal('Deposited credits.');
			}
			else {
				$this->logPayPal('Failed to deposit credits.');
			}
		}
		else {
			$this->logPayPal('Account/e-mail is not trusted, holding donation credits for %d hours.', $holdHours);
		}

		return array($trusted, $credits);
	}

	/**
	 * Log the transaction details into the same transactions table used by
	 * the classic IPN flow, so both show up together in the transaction log
	 * viewer. Many classic-only fields (payer_status, address_*, etc.) are
	 * not present in REST webhook payloads and are stored as NULL/empty.
	 * payer_email is only populated when HoldUntrustedAccount is enabled
	 * (see process()), since it requires an extra order lookup otherwise
	 * unneeded, and is required by Flux::processHeldCredits() to release
	 * held transactions and record trust once the hold period expires.
	 *
	 * @param Flux_LoginAthenaGroup $servGroup
	 * @param string $accountID
	 * @param string $serverName
	 * @param bool $trusted
	 * @param int $credits
	 * @param string $transactionID
	 * @param float $amount
	 * @param string $currencyCode
	 * @param string|null $paymentDate
	 * @param string|null $payerEmail
	 * @access private
	 */
	private function logToPayPalTable(Flux_LoginAthenaGroup $servGroup, $accountID, $serverName, $trusted, $credits, $transactionID, $amount, $currencyCode, $paymentDate = null, $payerEmail = null)
	{
		if (!$this->txnIsValid) {
			return;
		}

		$holdUntil = null;
		if (!$trusted) {
			$hours     = +(int)Flux::config('HoldUntrustedAccount');
			$holdUntil = date('Y-m-d H:i:s', time()+($hours*60*60));
		}

		$this->logPayPal('Saving transaction details to PayPal transactions table...');
		$sql = "
			INSERT INTO {$servGroup->loginDatabase}.{$this->txnLogTable} (
				account_id,
				server_name,
				credits,
				receiver_email,
				item_name,
				item_number,
				quantity,
				payment_status,
				pending_reason,
				payment_date,
				mc_gross,
				mc_fee,
				tax,
				mc_currency,
				parent_txn_id,
				txn_id,
				txn_type,
				first_name,
				last_name,
				address_street,
				address_city,
				address_state,
				address_zip,
				address_country,
				address_status,
				payer_email,
				payer_status,
				payment_type,
				notify_version,
				verify_sign,
				referrer_id,
				process_date,
				hold_until
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(),
				?
			)
		";
		$sth = $servGroup->connection->getStatement($sql);
		$ret = $sth->execute(array(
			$accountID,
			$serverName,
			$credits,
			null,             // receiver_email (not present in REST webhook)
			null,             // item_name
			null,             // item_number
			1,                // quantity
			'Completed',      // payment_status
			null,             // pending_reason
			$paymentDate,     // payment_date
			$amount,          // mc_gross
			null,             // mc_fee
			null,             // tax
			$currencyCode,    // mc_currency
			null,             // parent_txn_id
			$transactionID,   // txn_id
			'rest_webhook',   // txn_type
			null,             // first_name
			null,             // last_name
			null,             // address_street
			null,             // address_city
			null,             // address_state
			null,             // address_zip
			null,             // address_country
			null,             // address_status
			$payerEmail,      // payer_email (only fetched when HoldUntrustedAccount is enabled; null otherwise)
			null,             // payer_status
			null,             // payment_type
			null,             // notify_version
			null,             // verify_sign
			null,             // referrer_id
			$holdUntil
		));

		if ($ret) {
			$this->logPayPal('Stored information in PayPal transactions table for server %s.', $serverName);
		}
		else {
			$errorInfo = implode('/', $sth->errorInfo());
			$this->logPayPal('Failed to save information in PayPal transactions table. (%s)', $errorInfo);
		}
	}
}
?>
