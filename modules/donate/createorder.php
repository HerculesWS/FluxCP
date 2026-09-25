<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

require_once 'Flux/PayPalRestClient.php';

header('Content-Type: application/json');

$minimum = (float)Flux::config('MinDonationAmount');
$amount  = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.0;

if (!$amount || $amount < $minimum) {
	echo json_encode(array('error' => 'Invalid donation amount.'));
	exit;
}

$session    = Flux::$sessionData;
$customData = array(
	'server_name' => $session->loginAthenaGroup->serverName,
	'account_id'  => $session->account->account_id,
);

$restClient = new Flux_PayPalRestClient();
$order      = $restClient->createOrder($amount, Flux::config('DonationCurrency'), $customData);

if ($order && !empty($order['id'])) {
	echo json_encode(array('id' => $order['id']));
}
else {
	echo json_encode(array('error' => 'Failed to create PayPal order.'));
}

exit;
?>