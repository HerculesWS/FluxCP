<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Donate</h2>
<?php if (Flux::config('AcceptDonations')): ?>
	<?php if (!empty($errorMessage)): ?>
		<p class="red"><?php echo htmlspecialchars((string)$errorMessage) ?></p>
	<?php endif ?>
	
	<p>By donating, you're supporting the costs of <em>running</em> this server and <em>maintaining</em> it.  In return, you will be rewarded <span class="keyword">donation credits</span> that you may use to purchase items from our <a href="<?php echo htmlspecialchars($this->url('purchase')) ?>">item shop</a>.</p>
	<h3>Are you ready to donate?</h3>
	<p>All donations towards us are received by PayPal, but don't worry!  Even if you don't have an account with PayPal, you can still use your credit card to donate!</p>
		
	<?php
	$currency         = Flux::config('DonationCurrency');
	$dollarAmount     = (float)+Flux::config('CreditExchangeRate');
	$creditAmount     = 1;
	$rateMultiplier   = 10;
	$hoursHeld        = +(int)Flux::config('HoldUntrustedAccount');
	
	while ($dollarAmount < 1) {
		$dollarAmount  *= $rateMultiplier;
		$creditAmount  *= $rateMultiplier;
	}
	?>
	
	<?php if ($hoursHeld): ?>
		<div class="donate-hold">
		<p>To prevent fraudulent payments, our server currently locks the crediting process for
			<span class="hold-hours"><?php echo number_format($hoursHeld) ?> hours</span>
			after the donation has been made to ensure legitimate gameplay and a healthy PayPal reputation.</p>
		<p>This hold is applied only once for the associated PayPal e-mail and RO account.</p>
		</div>
	<?php endif ?>

	<div class="info-tiles">
		<div class="info-tile">
			<span class="info-tile-label">Current Credit Exchange Rate</span>
			<span class="info-tile-value"><?php echo $this->formatCurrency($dollarAmount) ?> <?php echo htmlspecialchars((string)$currency) ?>
			= <?php echo number_format($creditAmount) ?> credit(s)</span>
		</div>
		<div class="info-tile">
			<span class="info-tile-label">Minimum Donation Amount</span>
			<span class="info-tile-value"><?php echo $this->formatCurrency(Flux::config('MinDonationAmount')) ?> <?php echo htmlspecialchars((string)$currency) ?></span>
		</div>
	</div>
		
	<?php if (!$donationAmount): ?>
	<form action="<?php echo htmlspecialchars((string)$this->url) ?>" method="post">
		<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
		<input type="hidden" name="setamount" value="1" />
		<div class="donate-form">
			<p class="donate-form-title">Enter an amount you would like to donate</p>
			<div class="donate-fields">
				<label class="donate-field">
					<span class="donate-field-label"><?php echo htmlspecialchars((string)Flux::config('DonationCurrency')) ?></span>
					<input class="money-input" type="text" name="amount"
						value="<?php echo htmlspecialchars((string)$params->get('amount')) ?>"
						size="<?php echo (strlen((string)+Flux::config('CreditExchangeRate')) * 2) + 2 ?>" />
				</label>
				<span class="donate-or">or</span>
				<label class="donate-field">
					<span class="donate-field-label">Credits</span>
					<input class="credit-input" type="text" name="credit-amount"
						value="<?php echo htmlspecialchars(intval($params->get('amount') / Flux::config('CreditExchangeRate'))) ?>"
						size="<?php echo (strlen((string)+Flux::config('CreditExchangeRate')) * 2) + 2 ?>" />
				</label>
			</div>
			<input type="submit" value="Confirm Donation Amount" class="submit_button btn-primary" />
		</div>
	</form>
	<?php else: ?>
	<p>When you're ready to donate, click the big “Donate” button to proceed with your transaction.
		(You can choose to donate from your existing PayPal balance or use your credit card if you don't have an account).</p>

	<div class="donate-summary">
		<p class="credit-amount-text">
			<span class="credit-amount"><?php echo number_format(floor($donationAmount / Flux::config('CreditExchangeRate'))) ?></span>
			<span class="credit-amount-unit">credits</span>
		</p>
		<p class="donation-amount-text">Amount:
			<span class="donation-amount">
			<?php echo $this->formatCurrency($donationAmount) ?>
			<?php echo htmlspecialchars((string)Flux::config('DonationCurrency')) ?>
			</span>
		</p>
		<p class="reset-amount-text">
			<a href="<?php echo htmlspecialchars($this->url('donate', 'index', array('resetamount' => true))) ?>">Change amount</a>
		</p>
		<div class="donate-button"><?php echo $this->donateButton($donationAmount) ?></div>
	</div>
	<?php endif ?>
<?php else: ?>
	<p><?php echo Flux::message('NotAcceptingDonations') ?></p>
<?php endif ?>