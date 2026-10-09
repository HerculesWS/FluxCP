<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('PasswordChangeHeading')) ?></h2>
<?php if (!empty($errorMessage)): ?>
	<p class="red"><?php echo htmlspecialchars((string)$errorMessage) ?></p>
<?php else: ?>
	<p><?php echo htmlspecialchars(Flux::message('PasswordChangeInfo')) ?></p>
<?php endif ?>
<form action="<?php echo htmlspecialchars((string)$this->urlWithQs) ?>" method="post" class="generic-form form-stack form-narrow">
	<?php echo Flux_Security::csrfGenerate('PasswordEdit', true) ?>

	<div class="form-callout">
		<p><?php echo htmlspecialchars(Flux::message('PasswordChangeNote')) ?></p>
		<p class="important"><?php echo htmlspecialchars(Flux::message('PasswordChangeNote2')) ?></p>
	</div>

	<div class="form-row">
		<label for="currentpass"><?php echo htmlspecialchars(Flux::message('CurrentPasswordLabel')) ?></label>
		<input type="password" name="currentpass" id="currentpass" value="" />
	</div>

	<div class="form-row">
		<label for="newpass"><?php echo htmlspecialchars(Flux::message('NewPasswordLabel')) ?></label>
		<input type="password" name="newpass" id="newpass" value="" />
	</div>

	<div class="form-row">
		<label for="confirmnewpass"><?php echo htmlspecialchars(Flux::message('NewPasswordConfirmLabel')) ?></label>
		<input type="password" name="confirmnewpass" id="confirmnewpass" value="" />
	</div>

	<div class="form-actions">
		<input type="submit" value="<?php echo htmlspecialchars(Flux::message('PasswordChangeButton')) ?>" class="btn-primary" />
	</div>
</form>