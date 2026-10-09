<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Trusted PayPal E-mails</h2>
<?php if ($emails): ?>
<p>Below is a list of your trusted PayPal e-mail addresses.</p>
<div class="trusted-note">
	<p>Trusted e-mails do not undergo any holding process, therefore donations made by them will allow you to receive your credits <strong>instantly</strong>.</p>
</div>
<ul class="trusted-list">
	<?php foreach ($emails as $email): ?>
	<li class="trusted-item">
		<span class="trusted-email"><?php echo htmlspecialchars((string)$email->email) ?></span>
		<span class="trusted-date">Trusted since <?php echo $this->formatDateTime($email->create_date) ?></span>
	</li>
	<?php endforeach ?>
</ul>
<?php else: ?>
<p>You do not have any trusted PayPal e-mail addresses.</p>
<?php if (!Flux::config('HoldUntrustedAccount')): ?>
<p>This is most likely because the credit holding system is currently <strong>not in effect</strong>, which means a donation made from any e-mail address is immediately accredited.</p>
<?php endif ?>
<?php endif ?>