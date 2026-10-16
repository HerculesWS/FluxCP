<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('ResetPwTitle');

$account = $params->get('account');
$code    = $params->get('code');
$login   = $params->get('login');

$resetPassTable = Flux::config('FluxTables.ResetPasswordTable');

if (!$login || !$account || !$code || strlen($code) !== 32) {
	$this->deny();
}

$loginAthenaGroup = Flux::getServerGroupByName($login);
if (!$loginAthenaGroup) {
	$this->deny();
}

$sql = "SELECT userid, email FROM {$loginAthenaGroup->loginDatabase}.login WHERE account_id = ? LIMIT 1";
$sth = $loginAthenaGroup->connection->getStatement($sql);
$sth->execute(array($account));
$acc = $sth->fetch();

if (!$acc) {
	$this->deny();
}

$expireHours = (int)Flux::config('ResetPassExpireHours');
if ($expireHours < 1) {
	$expireHours = 24;
}

$sql  = "SELECT id FROM {$loginAthenaGroup->loginDatabase}.$resetPassTable WHERE ";
$sql .= "account_id = ? AND code = ? AND reset_done = 0 AND request_date > DATE_SUB(NOW(), INTERVAL $expireHours HOUR) LIMIT 1";
$sth  = $loginAthenaGroup->connection->getStatement($sql);

if (!$sth->execute(array($account, $code)) || !($reset=$sth->fetch())) {
	$this->deny();
}

// The link only opens the form, the person picks the new password themselves
// and it is never sent by e-mail.
if (count($_POST)) {
	$newPassword        = (string)$params->get('newpass');
	$confirmNewPassword = (string)$params->get('confirmnewpass');
	$passwordMinLength  = Flux::config('MinPasswordLength');
	$passwordMinUpper   = Flux::config('PasswordMinUpper');
	$passwordMinLower   = Flux::config('PasswordMinLower');
	$passwordMinNumber  = Flux::config('PasswordMinNumber');
	$passwordMinSymbol  = Flux::config('PasswordMinSymbol');

	if (!Flux_Security::csrfValidate('ResetPassword', $_POST, $csrfError)) {
		$errorMessage = $csrfError;
	}
	elseif (!$newPassword) {
		$errorMessage = Flux::message('NeedNewPassword');
	}
	elseif (!Flux::config('AllowUserInPassword') && stripos($newPassword, $acc->userid) !== false) {
		$errorMessage = Flux::message('NewPasswordHasUsername');
	}
	elseif (!ctype_graph($newPassword)) {
		$errorMessage = Flux::message('NewPasswordInvalid');
	}
	elseif (strlen($newPassword) < $passwordMinLength) {
		$errorMessage = sprintf(Flux::message('PasswordTooShort'), $passwordMinLength, Flux::config('MaxPasswordLength'));
	}
	elseif (strlen($newPassword) > Flux::config('MaxPasswordLength')) {
		$errorMessage = sprintf(Flux::message('PasswordTooLong'), $passwordMinLength, Flux::config('MaxPasswordLength'));
	}
	elseif (!$confirmNewPassword) {
		$errorMessage = Flux::message('ConfirmNewPassword');
	}
	elseif ($newPassword !== $confirmNewPassword) {
		$errorMessage = Flux::message('PasswordsDoNotMatch');
	}
	elseif ($passwordMinUpper > 0 && preg_match_all('/[A-Z]/', $newPassword, $matches) < $passwordMinUpper) {
		$errorMessage = sprintf(Flux::message('NewPasswordNeedUpper'), $passwordMinUpper);
	}
	elseif ($passwordMinLower > 0 && preg_match_all('/[a-z]/', $newPassword, $matches) < $passwordMinLower) {
		$errorMessage = sprintf(Flux::message('NewPasswordNeedLower'), $passwordMinLower);
	}
	elseif ($passwordMinNumber > 0 && preg_match_all('/[0-9]/', $newPassword, $matches) < $passwordMinNumber) {
		$errorMessage = sprintf(Flux::message('NewPasswordNeedNumber'), $passwordMinNumber);
	}
	elseif ($passwordMinSymbol > 0 && preg_match_all('/[^A-Za-z0-9]/', $newPassword, $matches) < $passwordMinSymbol) {
		$errorMessage = sprintf(Flux::message('NewPasswordNeedSymbol'), $passwordMinSymbol);
	}
	else {
		if ($loginAthenaGroup->loginServer->config->getUseMD5()) {
			$newPassword = Flux::hashPassword($newPassword);
		}

		// Use up the token first, only one request can get it.
		$sql  = "UPDATE {$loginAthenaGroup->loginDatabase}.$resetPassTable SET ";
		$sql .= "reset_done = 1, reset_date = NOW(), reset_ip = ?, new_password = NULL WHERE id = ? AND reset_done = 0";
		$sth  = $loginAthenaGroup->connection->getStatement($sql);

		if (!$sth->execute(array($_SERVER['REMOTE_ADDR'], $reset->id)) || $sth->rowCount() < 1) {
			$session->setMessageData(Flux::message('ResetPwFailed'));
			$this->redirect();
		}

		// Any other reset links still open for this account are no longer valid.
		$sql = "DELETE FROM {$loginAthenaGroup->loginDatabase}.$resetPassTable WHERE account_id = ? AND reset_done = 0";
		$sth = $loginAthenaGroup->connection->getStatement($sql);
		$sth->execute(array($account));

		$sql = "UPDATE {$loginAthenaGroup->loginDatabase}.login SET user_pass = ? WHERE account_id = ?";
		$sth = $loginAthenaGroup->connection->getStatement($sql);

		if (!$sth->execute(array($newPassword, $account))) {
			$session->setMessageData(Flux::message('ResetPwFailed'));
			$this->redirect();
		}

		$session->setMessageData(Flux::message('ResetPwDone'));
		$this->redirect($this->url('account', 'login'));
	}
}
?>
