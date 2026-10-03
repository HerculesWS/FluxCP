<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('TransferHeading')) ?></h2>
<?php if (!empty($errorMessage)): ?>
	<p class="red"><?php echo htmlspecialchars((string)$errorMessage) ?></p>
<?php endif ?>
<?php if ($session->account->balance): ?>
<h3><?php printf(htmlspecialchars(Flux::message('TransferSubHeading')), $server->serverName) ?></h3>
<p><?php printf(Flux::message('TransferInfo'), '<span class="remaining-balance">'.number_format($session->account->balance).'</span>') ?></p>
<p><?php echo htmlspecialchars(Flux::message('TransferInfo2')) ?></p>
<form action="<?php echo htmlspecialchars((string)$this->url) ?>" method="post" class="generic-form form-stack form-narrow">
	<?php echo $this->moduleActionFormInputs('account', 'transfer') ?>
	<?php echo Flux_Security::csrfGenerate('TransferCredit', true) ?>

	<div class="form-row">
		<label for="credits"><?php echo htmlspecialchars(Flux::message('TransferAmountLabel')) ?></label>
		<input type="text" name="credits" id="credits" value="<?php echo htmlspecialchars((string)$params->get('credits')) ?>" />
		<small class="field-hint"><?php echo htmlspecialchars(Flux::message('TransferAmountInfo')) ?></small>
	</div>

	<div class="form-row">
		<label for="char_name"><?php echo htmlspecialchars(Flux::message('TransferCharNameLabel')) ?></label>
		<input type="text" name="char_name" id="char_name" value="<?php echo htmlspecialchars((string)$params->get('char_name')) ?>" />
		<small class="field-hint"><?php echo htmlspecialchars(Flux::message('TransferCharNameInfo')) ?></small>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn-primary"
			onclick="return confirm('<?php echo htmlspecialchars(str_replace("'", "\'", Flux::message('TransferConfirm'))) ?>')">
			<?php echo htmlspecialchars(Flux::message('TransferButton')) ?>
		</button>
	</div>
</form>
<?php else: ?>
<p><?php echo htmlspecialchars(Flux::message('TransferNoCredits')) ?></p>
<?php endif ?>