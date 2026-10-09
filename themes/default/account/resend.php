<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('ResendHeading')) ?></h2>
<?php if (!empty($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars((string)$errorMessage) ?></p>
<?php endif ?>
<p><?php echo htmlspecialchars(Flux::message('ResendInfo')) ?></p>
<form action="<?php echo htmlspecialchars((string)$this->urlWithQs) ?>" method="post" class="generic-form form-stack form-narrow">
	<?php if (count($serverNames) > 1): ?>
	<div class="form-row">
		<label for="login"><?php echo htmlspecialchars(Flux::message('ResendServerLabel')) ?></label>
		<select name="login" id="login"<?php if (count($serverNames) === 1) echo ' disabled="disabled"' ?>>
		<?php foreach ($serverNames as $serverName): ?>
			<option value="<?php echo htmlspecialchars((string)$serverName) ?>"<?php if ($params->get('server') == $serverName) echo ' selected="selected"' ?>><?php echo htmlspecialchars((string)$serverName) ?></option>
		<?php endforeach ?>
		</select>
		<small class="field-hint"><?php echo htmlspecialchars(Flux::message('ResendServerInfo')) ?></small>
	</div>
	<?php endif ?>

	<div class="form-row">
		<label for="userid"><?php echo htmlspecialchars(Flux::message('ResendAccountLabel')) ?></label>
		<input type="text" name="userid" id="userid" />
		<small class="field-hint"><?php echo htmlspecialchars(Flux::message('ResendAccountInfo')) ?></small>
	</div>

	<div class="form-row">
		<label for="email"><?php echo htmlspecialchars(Flux::message('ResendEmailLabel')) ?></label>
		<input type="text" name="email" id="email" />
		<small class="field-hint"><?php echo htmlspecialchars(Flux::message('ResendEmailInfo')) ?></small>
	</div>

	<div class="form-actions">
		<input type="submit" value="<?php echo htmlspecialchars(Flux::message('ResendButton')) ?>" class="btn-primary" />
	</div>
</form>