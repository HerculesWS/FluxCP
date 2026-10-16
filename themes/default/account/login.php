<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('LoginHeading')) ?></h2>
<?php if (isset($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars((string)$errorMessage) ?></p>
<?php else: ?>

<?php if ($auth->actionAllowed('account', 'create')): ?>
<p><?php printf(Flux::message('LoginPageMakeAccount'), htmlspecialchars($this->url('account', 'create'))); ?></p>
<?php endif ?>

<?php endif ?>
<form action="<?php echo htmlspecialchars($this->url('account', 'login', array('return_url' => $params->get('return_url')))) ?>" method="post" class="generic-form form-stack register-form login-form">
	<?php echo Flux_Security::csrfGenerate('Login', true) ?>
	<?php if (count($serverNames) === 1): ?>
	<input type="hidden" name="server" value="<?php echo htmlspecialchars((string)$session->loginAthenaGroup->serverName) ?>">
	<?php endif ?>

	<div class="form-row">
		<label for="login_username"><?php echo htmlspecialchars(Flux::message('AccountUsernameLabel')) ?></label>
		<input type="text" name="username" id="login_username" autocomplete="username" value="<?php echo htmlspecialchars((string)$params->get('username')) ?>" />
	</div>

	<div class="form-row">
		<label for="login_password"><?php echo htmlspecialchars(Flux::message('AccountPasswordLabel')) ?></label>
		<div class="input-reveal">
			<input type="password" name="password" id="login_password" autocomplete="current-password" />
			<button type="button" class="reveal-toggle" data-target="login_password" aria-label="Show password">Show</button>
		</div>
		<?php if ($auth->actionAllowed('account', 'resetpass')): ?>
		<small class="field-hint field-hint-link"><a href="<?php echo htmlspecialchars($this->url('account', 'resetpass')) ?>">Forgot your password?</a></small>
		<?php endif ?>
	</div>

	<?php if (count($serverNames) > 1): ?>
	<div class="form-row">
		<label for="login_server"><?php echo htmlspecialchars(Flux::message('AccountServerLabel')) ?></label>
		<select name="server" id="login_server"<?php if (count($serverNames) === 1) echo ' disabled="disabled"' ?>>
			<?php foreach ($serverNames as $serverName): ?>
			<option value="<?php echo htmlspecialchars((string)$serverName) ?>"><?php echo htmlspecialchars((string)$serverName) ?></option>
			<?php endforeach ?>
		</select>
	</div>
	<?php endif ?>

	<?php if (Flux::config('UseLoginCaptcha')): ?>
		<?php if (Flux::config('EnableReCaptcha') && !Flux_ReCaptcha::isVisible()): ?>
		<?php // v3 is invisible; no labeled row, just emit the hidden token field. ?>
		<?php echo $recaptcha ?>
		<?php elseif (Flux::config('EnableReCaptcha')): ?>
	<div class="form-row">
		<span class="form-label"><?php echo htmlspecialchars(Flux::message('AccountSecurityLabel')) ?></span>
		<?php echo $recaptcha ?>
	</div>
		<?php else: ?>
	<div class="form-row">
		<label for="login_security_code"><?php echo htmlspecialchars(Flux::message('AccountSecurityLabel')) ?></label>
		<div class="security-code">
			<img src="<?php echo htmlspecialchars($this->url('captcha')) ?>" alt="Security code" />
		</div>
		<input type="text" name="security_code" id="login_security_code" autocomplete="off" />
		<div style="font-size: smaller;" class="action">
			<strong><a href="javascript:refreshSecurityCode('.security-code img')"><?php echo htmlspecialchars(Flux::message('RefreshSecurityCode')) ?></a></strong>
		</div>
	</div>
		<?php endif ?>
	<?php endif ?>

	<div class="form-actions">
		<button type="submit" class="btn-primary"><?php echo htmlspecialchars(Flux::message('LoginButton')) ?></button>
	</div>
</form>
<script>
// Show / hide toggle for the password field.
document.addEventListener('DOMContentLoaded', function () {
	var toggles = document.querySelectorAll('.reveal-toggle');
	for (var i = 0; i < toggles.length; ++i) {
		toggles[i].addEventListener('click', function () {
			var field = document.getElementById(this.getAttribute('data-target'));
			if (!field) {
				return;
			}
			var show = field.type === 'password';
			field.type = show ? 'text' : 'password';
			this.textContent = show ? 'Hide' : 'Show';
			this.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
		});
	}
});
</script>