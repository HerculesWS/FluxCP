<?php
if (!defined('FLUX_ROOT')) exit;

// Live availability check used by the register form. Returns JSON only.
header('Content-Type: application/json; charset=utf-8');

$respond = function ($available, $message) {
	echo json_encode(array('available' => $available, 'message' => $message));
	exit;
};

// Light throttle per client address: at most 60 checks a minute.
$rateFile = FLUX_DATA_DIR.'/tmp/checkrate_'.md5((string)$_SERVER['REMOTE_ADDR']).'.json';
$now      = time();
$state    = array('start' => $now, 'count' => 0);
if (is_file($rateFile)) {
	$stored = json_decode((string)file_get_contents($rateFile), true);
	if (is_array($stored) && isset($stored['start'], $stored['count']) && $now - $stored['start'] < 60) {
		$state = $stored;
	}
}
++$state['count'];
@file_put_contents($rateFile, json_encode($state));
if ($state['count'] > 60) {
	http_response_code(429);
	$respond(null, 'Too many checks. Please slow down.');
}

$field = (string)$params->get('field');
$value = trim((string)$params->get('value'));

$group = Flux::getServerGroupByName((string)$params->get('server'));
if (!$group) {
	$group = $session->loginAthenaGroup;
}
if (!$group || $value === '') {
	$respond(null, '');
}

if ($field === 'username') {
	if (preg_match('/[^' . Flux::config('UsernameAllowedChars') . ']/', $value)) {
		$respond(false, 'Only letters, numbers and underscores are allowed.');
	}
	elseif (strlen($value) < Flux::config('MinUsernameLength')) {
		$respond(false, sprintf('Username must be at least %d characters.', Flux::config('MinUsernameLength')));
	}
	elseif (strlen($value) > Flux::config('MaxUsernameLength')) {
		$respond(false, sprintf('Username must be %d characters or fewer.', Flux::config('MaxUsernameLength')));
	}
	elseif ($group->loginServer->usernameExists($value)) {
		$respond(false, 'That username is already taken.');
	}
	$respond(true, 'Username is available.');
}
elseif ($field === 'email') {
	if (Flux::config('EmailStrictCheck') && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
		$respond(false, 'Enter a valid email address.');
	}
	elseif (!preg_match('/^(.+?)@(.+?)$/', $value)) {
		$respond(false, 'Enter a valid email address.');
	}
	elseif (!Flux::config('AllowDuplicateEmails') && $group->loginServer->emailExists($value)) {
		$respond(false, 'That email is already registered.');
	}
	$respond(true, 'Email is available.');
}

$respond(null, '');
?>
