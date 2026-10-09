<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('EmailChangeHeading')) ?></h2>

<?php if (!empty($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars((string)$errorMessage) ?></p>
<?php endif ?>

<p><?php echo htmlspecialchars(Flux::message('EmailChangeInfo')) ?></p>

<?php if (Flux::config('RequireChangeConfirm')): ?>
<p><?php echo htmlspecialchars(Flux::message('EmailChangeInfo2')) ?></p>
<?php endif ?>

<form action="<?php echo htmlspecialchars((string)$this->urlWithQs) ?>" method="post" class="generic-form form-stack form-narrow">
	<?php echo Flux_Security::csrfGenerate('EmailEdit', true) ?>

	<div class="form-row">
		<label for="email"><?php echo htmlspecialchars(Flux::message('EmailChangeLabel')) ?></label>
		<input type="text" name="email" id="email" />
		<small class="field-hint"><?php echo htmlspecialchars(Flux::message('EmailChangeInputNote')) ?></small>
	</div>

	<div class="form-actions">
		<input type="submit" value="<?php echo htmlspecialchars(Flux::message('EmailChangeButton')) ?>" class="btn-primary" />
	</div>
</form>