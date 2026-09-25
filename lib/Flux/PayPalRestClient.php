<?php
require_once 'Flux/LogFile.php';

/**
 * Small client for PayPal's modern REST API + Webhooks (OAuth2, order
 * creation, and webhook signature verification).
 *
 * This coexists with the classic Flux_PaymentNotifyRequest IPN handler; it
 * is only used when 'PayPalMode' is set to 'rest' in the configuration.
 */
class Flux_PayPalRestClient {
	/**
	 * Logger class for logging to the PayPal REST log stored on disk.
	 *
	 * @access private
	 * @var Flux_LogFile
	 */
	private $logFile;

	/**
	 * Base API URL, selected by 'PayPalRestEnvironment'.
	 *
	 * @access private
	 * @var string
	 */
	private $apiBase;

	/**
	 * REST app client ID.
	 *
	 * @access private
	 * @var string
	 */
	private $clientID;

	/**
	 * REST app client secret.
	 *
	 * @access private
	 * @var string
	 */
	private $clientSecret;

	/**
	 * Webhook ID used to verify incoming webhook signatures.
	 *
	 * @access private
	 * @var string
	 */
	private $webhookID;

	/**
	 * Cached OAuth2 access token for this instance, so repeated calls
	 * within a single request don't re-fetch a new token every time.
	 *
	 * @access private
	 * @var string|null
	 */
	private $accessToken = null;

	/**
	 * Unix timestamp at which the cached access token expires.
	 *
	 * @access private
	 * @var int
	 */
	private $accessTokenExpiresAt = 0;

	/**
	 * Construct a new PayPal REST client instance, reading connection
	 * details from the application configuration.
	 *
	 * @access public
	 */
	public function __construct()
	{
		$this->logFile      = new Flux_LogFile(FLUX_DATA_DIR.'/logs/paypal_rest.log');
		$this->clientID     = Flux::config('PayPalRestClientID');
		$this->clientSecret = Flux::config('PayPalRestClientSecret');
		$this->webhookID    = Flux::config('PayPalWebhookID');

		$environment  = Flux::config('PayPalRestEnvironment');
		$this->apiBase = ($environment === 'live')
			? 'https://api-m.paypal.com'
			: 'https://api-m.sandbox.paypal.com';
	}

	/**
	 * Log to the PayPal REST log file. Works like printf().
	 *
	 * @param string $format
	 * @param mixed ...
	 * @return string
	 * @access protected
	 */
	protected function log()
	{
		$args = func_get_args();
		$func = array($this->logFile, 'puts');
		return call_user_func_array($func, $args);
	}

	/**
	 * Retrieve (and cache) an OAuth2 access token via the client_credentials
	 * grant.
	 *
	 * @return string|bool Access token, or false on failure.
	 * @access public
	 */
	public function getAccessToken()
	{
		if ($this->accessToken && time() < $this->accessTokenExpiresAt) {
			return $this->accessToken;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiBase.'/v1/oauth2/token');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
		curl_setopt($ch, CURLOPT_USERPWD, $this->clientID.':'.$this->clientSecret);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);

		$response = curl_exec($ch);
		$errno    = curl_errno($ch);
		$errstr   = curl_error($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($response === false || $errno) {
			$this->log('Failed to connect to PayPal for OAuth2 token: [%d] %s', $errno, $errstr);
			return false;
		}

		$data = json_decode($response, true);

		if ($httpCode != 200 || !is_array($data) || empty($data['access_token'])) {
			$this->log('Failed to obtain OAuth2 access token (HTTP %d): %s', $httpCode, $response);
			return false;
		}

		$this->accessToken          = $data['access_token'];
		$expiresIn                  = isset($data['expires_in']) ? (int)$data['expires_in'] : 300;
		$this->accessTokenExpiresAt = time() + max(0, $expiresIn - 30); // Refresh a bit early.

		return $this->accessToken;
	}

	/**
	 * Verify the signature of an incoming webhook notification against
	 * PayPal, using the /v1/notifications/verify-webhook-signature endpoint.
	 *
	 * @param array $headers HTTP headers received with the webhook request.
	 * @param string $rawBody Raw JSON body received with the webhook request.
	 * @return bool True if the signature is verified, false otherwise.
	 * @access public
	 */
	public function verifyWebhookSignature(array $headers, $rawBody)
	{
		$token = $this->getAccessToken();
		if (!$token) {
			$this->log('Cannot verify webhook signature, no access token available.');
			return false;
		}

		$normalizedHeaders = array();
		foreach ($headers as $key => $value) {
			$normalizedHeaders[strtolower($key)] = $value;
		}

		$transmissionID  = isset($normalizedHeaders['paypal-transmission-id']) ? $normalizedHeaders['paypal-transmission-id'] : '';
		$transmissionTime = isset($normalizedHeaders['paypal-transmission-time']) ? $normalizedHeaders['paypal-transmission-time'] : '';
		$certUrl         = isset($normalizedHeaders['paypal-cert-url']) ? $normalizedHeaders['paypal-cert-url'] : '';
		$authAlgo        = isset($normalizedHeaders['paypal-auth-algo']) ? $normalizedHeaders['paypal-auth-algo'] : '';
		$transmissionSig = isset($normalizedHeaders['paypal-transmission-sig']) ? $normalizedHeaders['paypal-transmission-sig'] : '';

		$webhookEvent = json_decode($rawBody, true);
		if (!is_array($webhookEvent)) {
			$this->log('Cannot verify webhook signature, webhook body is not valid JSON.');
			return false;
		}

		$payload = array(
			'transmission_id'   => $transmissionID,
			'transmission_time' => $transmissionTime,
			'cert_url'          => $certUrl,
			'auth_algo'         => $authAlgo,
			'transmission_sig'  => $transmissionSig,
			'webhook_id'        => $this->webhookID,
			'webhook_event'     => $webhookEvent,
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiBase.'/v1/notifications/verify-webhook-signature');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token,
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);

		$response = curl_exec($ch);
		$errno    = curl_errno($ch);
		$errstr   = curl_error($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($response === false || $errno) {
			$this->log('Failed to connect to PayPal for webhook signature verification: [%d] %s', $errno, $errstr);
			return false;
		}

		$data = json_decode($response, true);

		if ($httpCode != 200 || !is_array($data)) {
			$this->log('Unexpected response verifying webhook signature (HTTP %d): %s', $httpCode, $response);
			return false;
		}

		if (isset($data['verification_status']) && $data['verification_status'] === 'SUCCESS') {
			$this->log('Webhook signature verified successfully. (transmission_id: %s)', $transmissionID);
			return true;
		}
		else {
			$this->log('Webhook signature verification failed. (recv: %s)', $response);
			return false;
		}
	}

	/**
	 * Create a PayPal order (v2/checkout/orders) with intent=CAPTURE, for
	 * the given amount/currency. Custom data (account_id/server_name) is
	 * stashed in the purchase unit's custom_id field, using the same
	 * base64+serialize format as the classic IPN button.
	 *
	 * NOTE: PayPal's custom_id field is limited to 255 characters
	 * (confirmed against the live sandbox API; PayPal's own docs are
	 * inconsistent about the exact figure). Most account_id/server_name
	 * combinations comfortably fit within that once base64+serialize'd,
	 * but extremely long server names could theoretically overflow it --
	 * keep server names reasonably short.
	 *
	 * @param float $amount
	 * @param string $currency
	 * @param array $customData
	 * @return array|bool Decoded order response (with 'id' and 'links'), or false on failure.
	 * @access public
	 */
	public function createOrder($amount, $currency, array $customData)
	{
		$token = $this->getAccessToken();
		if (!$token) {
			$this->log('Cannot create order, no access token available.');
			return false;
		}

		$customID = base64_encode(serialize($customData));

		$payload = array(
			'intent'         => 'CAPTURE',
			'purchase_units' => array(
				array(
					'custom_id' => $customID,
					'amount'    => array(
						'currency_code' => $currency,
						'value'         => number_format((float)$amount, 2, '.', ''),
					),
				),
			),
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiBase.'/v2/checkout/orders');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token,
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);

		$response = curl_exec($ch);
		$errno    = curl_errno($ch);
		$errstr   = curl_error($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($response === false || $errno) {
			$this->log('Failed to connect to PayPal for order creation: [%d] %s', $errno, $errstr);
			return false;
		}

		$data = json_decode($response, true);

		if (($httpCode != 200 && $httpCode != 201) || !is_array($data) || empty($data['id'])) {
			$this->log('Failed to create order (HTTP %d): %s', $httpCode, $response);
			return false;
		}

		$this->log('Created order %s for %s %s.', $data['id'], $currency, $amount);

		return $data;
	}

	/**
	 * Fetch an existing order (GET /v2/checkout/orders/{id}). Used to look
	 * up the payer's e-mail address for a completed capture, since
	 * PAYMENT.CAPTURE.COMPLETED webhook payloads don't include it directly
	 * -- only the order they came from does (via payer.email_address).
	 *
	 * @param string $orderID
	 * @return array|bool Decoded order response, or false on failure.
	 * @access public
	 */
	public function getOrder($orderID)
	{
		$token = $this->getAccessToken();
		if (!$token) {
			$this->log('Cannot fetch order, no access token available.');
			return false;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiBase.'/v2/checkout/orders/'.urlencode($orderID));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Bearer '.$token,
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);

		$response = curl_exec($ch);
		$errno    = curl_errno($ch);
		$errstr   = curl_error($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($response === false || $errno) {
			$this->log('Failed to connect to PayPal to fetch order %s: [%d] %s', $orderID, $errno, $errstr);
			return false;
		}

		$data = json_decode($response, true);

		if ($httpCode != 200 || !is_array($data)) {
			$this->log('Failed to fetch order %s (HTTP %d): %s', $orderID, $httpCode, $response);
			return false;
		}

		return $data;
	}
}
?>
