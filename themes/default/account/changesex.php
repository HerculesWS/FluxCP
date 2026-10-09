<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('GenderChangeHeading')) ?></h2>
<?php if ($cost): ?>
<p>
	<?php printf(Flux::message('GenderChangeCost'), '<span class="remaining-balance">'.number_format((int)$cost).'</span>') ?>
	<?php printf(Flux::message('GenderChangeBalance'), '<span class="remaining-balance">'.number_format((int)$session->account->balance).'</span>') ?>
</p>
<?php if (!$hasNecessaryFunds): ?>
<p><?php echo htmlspecialchars(Flux::message('GenderChangeNoFunds')) ?></p>
<?php elseif ($auth->allowedToAvoidSexChangeCost): ?>
<p><?php echo htmlspecialchars(Flux::message('GenderChangeNoCost')) ?></p>
<?php endif ?>
<?php endif ?>

<?php if ($hasNecessaryFunds): ?>
<?php if (empty($errorMessage)): ?>
<div class="form-callout form-callout-warn">
	<p><strong><?php echo htmlspecialchars(Flux::message('NoteLabel')) ?>:</strong> <?php printf(Flux::message('GenderChangeCharInfo'), '<em>'.implode(', ', array_values($badJobs)).'</em>') ?>.</p>
</div>
<h3><?php echo htmlspecialchars(Flux::message('GenderChangeSubHeading')) ?></h3>
<?php else: ?>
<p class="red"><?php echo htmlspecialchars((string)$errorMessage) ?></p>
<?php endif ?>
<form action="<?php echo htmlspecialchars((string)$this->urlWithQs) ?>" method="post" class="generic-form form-stack form-narrow">
	<input type="hidden" name="changegender" value="1" />
	<?php echo Flux_Security::csrfGenerate('GenderEdit', true) ?>

	<p class="form-lead">
		<?php printf(Flux::message('GenderChangeFormText'), '<strong>'.strtolower($this->genderText($session->account->sex == 'M' ? 'F' : 'M')).'</strong>') ?>
	</p>

	<div class="form-actions">
		<button type="submit" class="btn-primary"
			onclick="return confirm('<?php echo str_replace("'", "\'", Flux::message('GenderChangeConfirm')) ?>')">
			<?php echo htmlspecialchars(Flux::message('GenderChangeButton')) ?>
		</button>
	</div>
</form>
<?php endif ?>