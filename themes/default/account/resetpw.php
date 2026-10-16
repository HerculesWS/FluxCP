<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('ResetPwTitle')) ?></h2>
<?php if (!empty($errorMessage)): ?>
	<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php else: ?>
	<p><?php echo htmlspecialchars(Flux::message('ResetPwInfo')) ?></p>
<?php endif ?>
<br />
<form action="<?php echo htmlspecialchars($this->urlWithQs) ?>" method="post" class="generic-form">
	<?php echo Flux_Security::csrfGenerate('ResetPassword', true) ?>

	<table class="generic-form-table">
		<tr>
			<th><label for="newpass"><?php echo htmlspecialchars(Flux::message('NewPasswordLabel')) ?></label></th>
			<td><input type="password" name="newpass" id="newpass" value="" /></td>
		</tr>
		<tr>
			<th><label for="confirmnewpass"><?php echo htmlspecialchars(Flux::message('NewPasswordConfirmLabel')) ?></label></th>
			<td><input type="password" name="confirmnewpass" id="confirmnewpass" value="" /></td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<input type="submit" value="<?php echo htmlspecialchars(Flux::message('PasswordChangeButton')) ?>" />
			</td>
		</tr>
	</table>
</form>
