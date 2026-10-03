<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('AccountCreateHeading')) ?></h2>
<p><?php printf(htmlspecialchars(Flux::message('AccountCreateInfo')), '<a href="'.$this->url('service', 'tos').'">'.Flux::message('AccountCreateTerms').'</a>') ?></p>
<div class="form-callout password-rules">
<p class="notes-heading"><strong>Notes:</strong></p>
<ul class="rule-list">
<?php if (Flux::config('RequireEmailConfirm')): ?>
	<li>You will need to provide a working e-mail address to confirm your account before you can log-in.</li>
<?php endif ?>
	<li data-rule="length" data-min="<?php echo (int)Flux::config('MinPasswordLength') ?>" data-max="<?php echo (int)Flux::config('MaxPasswordLength') ?>"><?php echo sprintf("Your password must be between %d and %d characters.", Flux::config('MinPasswordLength'), Flux::config('MaxPasswordLength')) ?></li>
<?php if (Flux::config('PasswordMinUpper') > 0): ?>
	<li data-rule="upper" data-min="<?php echo (int)Flux::config('PasswordMinUpper') ?>"><?php echo sprintf(Flux::message('PasswordNeedUpper'), Flux::config('PasswordMinUpper')) ?></li>
<?php endif ?>
<?php if (Flux::config('PasswordMinLower') > 0): ?>
	<li data-rule="lower" data-min="<?php echo (int)Flux::config('PasswordMinLower') ?>"><?php echo sprintf(Flux::message('PasswordNeedLower'), Flux::config('PasswordMinLower')) ?></li>
<?php endif ?>
<?php if (Flux::config('PasswordMinNumber') > 0): ?>
	<li data-rule="number" data-min="<?php echo (int)Flux::config('PasswordMinNumber') ?>"><?php echo sprintf(Flux::message('PasswordNeedNumber'), Flux::config('PasswordMinNumber')) ?></li>
<?php endif ?>
<?php if (Flux::config('PasswordMinSymbol') > 0): ?>
	<li data-rule="symbol" data-min="<?php echo (int)Flux::config('PasswordMinSymbol') ?>"><?php echo sprintf(Flux::message('PasswordNeedSymbol'), Flux::config('PasswordMinSymbol')) ?></li>
<?php endif ?>
<?php if (!Flux::config('AllowUserInPassword')): ?>
	<li data-rule="nouser"><?php echo Flux::message('PasswordContainsUser') ?></li>
<?php endif ?>
</ul>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var items = document.querySelectorAll('.password-rules li[data-rule]');
	var pass = document.querySelector('input[name=password]');
	var user = document.querySelector('input[name=username]');
	if (!items.length || !pass) {
		return;
	}
	var count = function (value, re) {
		var m = value.match(re);
		return m ? m.length : 0;
	};
	var check = function () {
		var value = pass.value;
		var name = user ? user.value.toLowerCase() : '';
		for (var i = 0; i < items.length; ++i) {
			var li = items[i], rule = li.getAttribute('data-rule');
			var min = parseInt(li.getAttribute('data-min'), 10) || 0;
			var ok = false;
			if (rule === 'length') {
				ok = value.length >= min && value.length <= (parseInt(li.getAttribute('data-max'), 10) || 1e9);
			}
			else if (rule === 'upper') { ok = count(value, /[A-Z]/g) >= min; }
			else if (rule === 'lower') { ok = count(value, /[a-z]/g) >= min; }
			else if (rule === 'number') { ok = count(value, /[0-9]/g) >= min; }
			else if (rule === 'symbol') { ok = count(value, /[^A-Za-z0-9]/g) >= min; }
			else if (rule === 'nouser') { ok = name === '' || value.toLowerCase().indexOf(name) === -1; }
			li.className = value === '' ? '' : (ok ? 'rule-met' : 'rule-unmet');
		}
	};
	pass.addEventListener('input', check);
	if (user) {
		user.addEventListener('input', check);
	}
	check();
});
</script>
<?php if (isset($errorMessage)): ?>
<p class="red" style="font-weight: bold"><?php echo htmlspecialchars((string)$errorMessage) ?></p>
<?php endif ?>
<form action="<?php echo htmlspecialchars((string)$this->url) ?>" method="post" class="generic-form form-stack register-form">
	<?php if (count($serverNames) === 1): ?>
	<input type="hidden" name="server" value="<?php echo htmlspecialchars((string)$session->loginAthenaGroup->serverName) ?>">
	<?php endif ?>

	<?php if (count($serverNames) > 1): ?>
	<div class="form-row">
		<label for="register_server"><?php echo htmlspecialchars(Flux::message('AccountServerLabel')) ?></label>
		<select name="server" id="register_server"<?php if (count($serverNames) === 1) echo ' disabled="disabled"' ?>>
		<?php foreach ($serverNames as $serverName): ?>
			<option value="<?php echo htmlspecialchars((string)$serverName) ?>"<?php if ($params->get('server') == $serverName) echo ' selected="selected"' ?>><?php echo htmlspecialchars((string)$serverName) ?></option>
		<?php endforeach ?>
		</select>
	</div>
	<?php endif ?>

	<div class="form-row">
		<label for="register_username"><?php echo htmlspecialchars(Flux::message('AccountUsernameLabel')) ?></label>
		<input type="text" name="username" id="register_username" autocomplete="username"
			data-min="<?php echo (int)Flux::config('MinUsernameLength') ?>"
			data-max="<?php echo (int)Flux::config('MaxUsernameLength') ?>"
			data-chars="<?php echo htmlspecialchars((string)Flux::config('UsernameAllowedChars')) ?>"
			value="<?php echo htmlspecialchars((string)$params->get('username')) ?>" />
		<small class="field-status" id="username_status" aria-live="polite"></small>
	</div>

	<div class="form-row">
		<label for="register_password"><?php echo htmlspecialchars(Flux::message('AccountPasswordLabel')) ?></label>
		<div class="input-reveal">
			<input type="password" name="password" id="register_password" autocomplete="new-password" />
			<button type="button" class="reveal-toggle" data-target="register_password" aria-label="Show password">Show</button>
		</div>
	</div>

	<div class="form-row">
		<label for="register_confirm_password"><?php echo htmlspecialchars(Flux::message('AccountPassConfirmLabel')) ?></label>
		<div class="input-reveal">
			<input type="password" name="confirm_password" id="register_confirm_password" autocomplete="new-password" />
			<button type="button" class="reveal-toggle" data-target="register_confirm_password" aria-label="Show password">Show</button>
		</div>
		<small class="field-status" id="confirm_status" aria-live="polite"></small>
	</div>

	<div class="form-row">
		<label for="register_email_address"><?php echo htmlspecialchars(Flux::message('AccountEmailLabel')) ?></label>
		<input type="text" name="email_address" id="register_email_address" autocomplete="email" inputmode="email" value="<?php echo htmlspecialchars((string)$params->get('email_address')) ?>" />
		<small class="field-status" id="email_status" aria-live="polite"></small>
	</div>

	<div class="form-row">
		<label for="register_gender"><?php echo htmlspecialchars(Flux::message('AccountGenderLabel')) ?></label>
		<select name="gender" id="register_gender">
			<option value="">Select gender</option>
			<option value="M"<?php if ($params->get('gender') === 'M') echo ' selected="selected"' ?>><?php echo $this->genderText('M') ?></option>
			<option value="F"<?php if ($params->get('gender') === 'F') echo ' selected="selected"' ?>><?php echo $this->genderText('F') ?></option>
		</select>
		<?php $genderWarning = Flux::config('GenderRegisterWarning'); if (is_null($genderWarning) || $genderWarning): ?>
		<small class="field-hint"><?php echo htmlspecialchars(Flux::message('AccountCreateGenderInfo')) ?></small>
		<?php endif ?>
	</div>

	<div class="form-row">
		<span class="form-label"><?php echo htmlspecialchars(Flux::message('AccountBirthdateLabel')) ?></span>
		<?php
		$bYear  = (string)$params->get('birthdate_year');
		$bMonth = (string)$params->get('birthdate_month');
		$bDay   = (string)$params->get('birthdate_day');
		?>
		<div class="birthdate-row" data-progressive>
			<span class="date-field">
				<select name="birthdate_year" aria-label="Year">
					<option value="">Year</option>
					<?php for ($y = (int)date('Y'); $y >= (int)date('Y') - 100; --$y): ?>
					<option value="<?php printf('%04d', $y) ?>"<?php if ($bYear !== '' && (int)$bYear === $y) echo ' selected="selected"' ?>><?php printf('%04d', $y) ?></option>
					<?php endfor ?>
				</select>
				<select name="birthdate_month" aria-label="Month"<?php if ($bYear === '') echo ' hidden' ?>>
					<option value="">Month</option>
					<?php for ($m = 1; $m <= 12; ++$m): ?>
					<option value="<?php printf('%02d', $m) ?>"<?php if ($bMonth !== '' && (int)$bMonth === $m) echo ' selected="selected"' ?>><?php printf('%02d', $m) ?></option>
					<?php endfor ?>
				</select>
				<select name="birthdate_day" aria-label="Day"<?php if ($bYear === '' || $bMonth === '') echo ' hidden' ?>>
					<option value="">Day</option>
					<?php for ($d = 1; $d <= 31; ++$d): ?>
					<option value="<?php printf('%02d', $d) ?>"<?php if ($bDay !== '' && (int)$bDay === $d) echo ' selected="selected"' ?>><?php printf('%02d', $d) ?></option>
					<?php endfor ?>
				</select>
			</span>
		</div>
	</div>

	<?php if (Flux::config('UseCaptcha')): ?>
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
		<label for="register_security_code"><?php echo htmlspecialchars(Flux::message('AccountSecurityLabel')) ?></label>
		<div class="security-code">
			<img src="<?php echo htmlspecialchars($this->url('captcha')) ?>" alt="Security code" />
		</div>

		<input type="text" name="security_code" id="register_security_code" autocomplete="off" />
		<div style="font-size: smaller;" class="action">
			<strong><a href="javascript:refreshSecurityCode('.security-code img')"><?php echo htmlspecialchars(Flux::message('RefreshSecurityCode')) ?></a></strong>
		</div>
	</div>
		<?php endif ?>
	<?php endif ?>

	<p class="form-note">
		<?php printf(htmlspecialchars(Flux::message('AccountCreateInfo2')), '<a href="'.$this->url('service', 'tos').'">'.Flux::message('AccountCreateTerms').'</a>') ?>
	</p>

	<div class="form-actions">
		<button type="submit" class="btn-primary"><?php echo htmlspecialchars(Flux::message('AccountCreateButton')) ?></button>
	</div>
</form>
<script>
// The password rules move under the password field and show while it is being filled in.
document.addEventListener('DOMContentLoaded', function () {
	var rules = document.querySelector('.password-rules');
	var password = document.getElementById('register_password');
	if (!rules || !password) {
		return;
	}
	var row = password.parentNode;
	while (row && !/(^|\s)form-row(\s|$)/.test(row.className)) {
		row = row.parentNode;
	}
	if (!row) {
		return;
	}
	row.appendChild(rules);
	rules.className += ' rules-inline';
	rules.hidden = true;

	var allMet = function () {
		return password.value !== '' && rules.querySelectorAll('li[data-rule]:not(.rule-met)').length === 0;
	};
	password.addEventListener('focus', function () {
		rules.hidden = false;
	});
	password.addEventListener('input', function () {
		rules.hidden = false;
	});
	password.addEventListener('blur', function () {
		if (password.value === '' || allMet()) {
			rules.hidden = true;
		}
	});
});

// Live availability checks (username / email) against the server, shortly after typing stops.
var fluxCheckUrl = <?php echo json_encode($this->url('account', 'check')) ?>;
function fluxAvailability(field, value, done) {
	var server = document.querySelector('[name=server]');
	var url = fluxCheckUrl + (fluxCheckUrl.indexOf('?') === -1 ? '?' : '&')
		+ 'field=' + encodeURIComponent(field) + '&value=' + encodeURIComponent(value)
		+ (server ? '&server=' + encodeURIComponent(server.value) : '');
	var request = new XMLHttpRequest();
	request.open('GET', url, true);
	request.onreadystatechange = function () {
		if (request.readyState !== 4) {
			return;
		}
		try {
			done(JSON.parse(request.responseText));
		}
		catch (e) {
			done(null);
		}
	};
	request.send();
}

document.addEventListener('DOMContentLoaded', function () {
	var input = document.getElementById('register_username');
	var status = document.getElementById('username_status');
	if (!input || !status) {
		return;
	}
	var min = parseInt(input.getAttribute('data-min'), 10) || 0;
	var max = parseInt(input.getAttribute('data-max'), 10) || 0;
	var badChars = null;
	try {
		badChars = new RegExp('[^' + input.getAttribute('data-chars') + ']');
	}
	catch (e) {
		badChars = null;
	}
	var touched = input.value !== '';
	var timer = null;
	var serial = 0;

	var show = function (kind, message) {
		input.className = input.className.replace(/\s*field-(ok|bad)/g, '');
		status.className = 'field-status' + (kind ? ' ' + kind : '');
		status.textContent = message;
		if (kind === 'ok' || kind === 'bad') {
			input.className += ' field-' + kind;
		}
	};

	var check = function () {
		window.clearTimeout(timer);
		var value = input.value;
		++serial;
		if (value === '' || !touched) {
			show('', '');
			return;
		}
		if (badChars && badChars.test(value)) {
			show('bad', 'Only letters, numbers and underscores are allowed.');
			return;
		}
		if (min && value.length < min) {
			// Too short while typing is not an error yet; it becomes one when the field is left.
			show('', input === document.activeElement ? '' : 'Username must be at least ' + min + ' characters.');
			if (input !== document.activeElement) {
				status.className += ' bad';
			}
			return;
		}
		if (max && value.length > max) {
			show('bad', 'Username must be ' + max + ' characters or fewer.');
			return;
		}
		show('wait', 'Checking availability\u2026');
		var mine = serial;
		timer = window.setTimeout(function () {
			fluxAvailability('username', value, function (result) {
				if (mine !== serial || !result) {
					if (mine === serial) {
						show('', '');
					}
					return;
				}
				if (result.available === true) {
					show('ok', 'Username is available');
				}
				else if (result.available === false) {
					show('bad', result.message);
				}
				else {
					show('', '');
				}
			});
		}, 450);
	};

	input.addEventListener('input', function () {
		touched = true;
		check();
	});
	input.addEventListener('blur', function () {
		touched = true;
		check();
	});
	if (touched) {
		check();
	}
});

// Email format check: shown once the field has been left, then kept up to date while editing.
document.addEventListener('DOMContentLoaded', function () {
	var email = document.getElementById('register_email_address');
	var status = document.getElementById('email_status');
	if (!email || !status) {
		return;
	}
	var touched = email.value !== '';
	var emailTimer = null;
	var emailSerial = 0;
	var valid = function (value) {
		return /^[^\s@]+@[^\s@.]+(\.[^\s@.]+)+$/.test(value) && /\.[^\s@.]{2,}$/.test(value);
	};
	var check = function () {
		++emailSerial;
		window.clearTimeout(emailTimer);
		email.className = email.className.replace(/\s*field-(ok|bad)/g, '');
		status.className = 'field-status';
		status.textContent = '';
		var value = email.value.replace(/^\s+|\s+$/g, '');
		if (value === '' || !touched) {
			return;
		}
		if (valid(value)) {
			status.className += ' wait';
			status.textContent = 'Checking\u2026';
			var mine = ++emailSerial;
			window.clearTimeout(emailTimer);
			emailTimer = window.setTimeout(function () {
				fluxAvailability('email', value, function (result) {
					if (mine !== emailSerial) {
						return;
					}
					status.className = 'field-status';
					email.className = email.className.replace(/\s*field-(ok|bad)/g, '');
					if (result && result.available === false) {
						email.className += ' field-bad';
						status.className += ' bad';
						status.textContent = result.message;
					}
					else {
						email.className += ' field-ok';
						status.className += ' ok';
						status.textContent = 'Looks good';
					}
				});
			}, 450);
		}
		else {
			email.className += ' field-bad';
			status.className += ' bad';
			status.textContent = 'Enter a valid email address, like name@example.com';
		}
	};
	email.addEventListener('blur', function () {
		touched = true;
		check();
	});
	email.addEventListener('input', check);
	check();
});

// Live check that the confirmation matches the password.
document.addEventListener('DOMContentLoaded', function () {
	var password = document.getElementById('register_password');
	var confirm = document.getElementById('register_confirm_password');
	var status = document.getElementById('confirm_status');
	if (!password || !confirm || !status) {
		return;
	}
	var check = function () {
		confirm.className = confirm.className.replace(/\s*field-(ok|bad)/g, '');
		status.className = 'field-status';
		status.textContent = '';
		if (confirm.value === '') {
			return;
		}
		if (confirm.value === password.value) {
			confirm.className += ' field-ok';
			status.className += ' ok';
			status.textContent = 'Passwords match';
		}
		else {
			confirm.className += ' field-bad';
			status.className += ' bad';
			status.textContent = 'Passwords do not match';
		}
	};
	password.addEventListener('input', check);
	confirm.addEventListener('input', check);
});

// Show / hide toggle for the password fields.
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