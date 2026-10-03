<?php
if (!defined('FLUX_ROOT')) exit;

require_once 'Flux/PaymentNotifyRequestRest.php';
$rawBody = file_get_contents('php://input');
if ($rawBody) {
	$headers = function_exists('getallheaders') ? getallheaders() : array();
	$request = new Flux_PaymentNotifyRequestRest($rawBody, $headers);
	$request->process();
}
exit;
?>