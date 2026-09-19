<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'Change Sex';

$charID = $params->get('id');
if (!$charID) {
	$this->deny();
}

$char = $server->getCharacter($charID);
if (!$char || ($char->account_id != $session->account->account_id && !$auth->allowedToChangeSex)) {
	$this->deny();
}

if (!Flux_Security::csrfValidate('Session', $_GET, $error) ) {
	$session->setMessageData($error);
	$this->redirect($this->url('character', 'view', array('id' => $charID)));
}

if ($char->sex == 'U') {
	$sql  = "SELECT sex FROM {$server->loginDatabase}.login WHERE account_id = ? LIMIT 1";
	$sth  = $server->connection->getStatement($sql);
	$sth->execute(array($char->account_id));
	$account   = $sth->fetch();
	$currentSex = $account ? $account->sex : 'M';
}
else {
	$currentSex = $char->sex;
}

$newSex = $currentSex == 'M' ? 'F' : 'M';
$result = $server->changeSex($charID, $newSex);

if ($result === -1) {
	$message = sprintf(Flux::message('CantChangeSexWhenOnline'), $char->name);
}
elseif ($result === true) {
	$message = sprintf(Flux::message('ChangeSexSuccessful'), $char->name);
}
else {
	$message = sprintf(Flux::message('ChangeSexFailed'), $char->name);
}

$session->setMessageData($message);
$this->redirect($this->url('character', 'view', array('id' => $charID)));
?>
