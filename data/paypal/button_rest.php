<?php
if (!defined('FLUX_ROOT')) exit;

if (empty($amount)) {
	return false;
}

$clientID         = htmlspecialchars(Flux::config('PayPalRestClientID'));
$donationCurrency = htmlspecialchars(Flux::config('DonationCurrency'));
$amountValue      = number_format((float)$amount, 2, '.', '');
$createOrderUrl   = $this->url('donate', 'createorder');

$completeUrlExists = file_exists(FLUX_ROOT.'/'.FLUX_THEME_DIR.'/'.$this->getName().'/donate/complete.php');
$completeUrl       = $completeUrlExists ? $this->url('donate', 'complete') : null;
?>
<div style="max-width: 300px; margin: 0 auto">
	<div id="paypal-button-container"></div>
</div>
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo $clientID ?>&currency=<?php echo $donationCurrency ?>&intent=capture"></script>
<script>
paypal.Buttons({
	createOrder: function(data, actions) {
		return fetch(<?php echo json_encode($createOrderUrl) ?>, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: 'amount=' + encodeURIComponent(<?php echo json_encode($amountValue) ?>)
		}).then(function(res) {
			return res.json();
		}).then(function(order) {
			if (order.error || !order.id) {
				throw new Error(order.error || 'Unable to create order.');
			}
			return order.id;
		});
	},
	onApprove: function(data, actions) {
		// Capturing here confirms the payment with PayPal, but crediting
		// the game account only ever happens server-side once the
		// PAYMENT.CAPTURE.COMPLETED webhook arrives at notifyrest.php --
		// client-side capture alone can't be trusted for crediting.
		return actions.order.capture().then(function() {
<?php if ($completeUrl): ?>
			window.location.href = <?php echo json_encode($completeUrl) ?>;
<?php else: ?>
			alert('Thank you, your donation is being processed. Credits will be applied shortly.');
<?php endif ?>
		});
	},
	onError: function(err) {
		alert('There was a problem processing your donation. Please try again.');
	}
}).render('#paypal-button-container');
</script>
