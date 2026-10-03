<?php
if (!defined('FLUX_ROOT')) exit;
require_once dirname(__FILE__) . '/_layout.php';

$siteTitle = Flux::config('SiteTitle');

echo fluxEmailLayout(array(
	'title'     => sprintf('%s: Change E-mail', $siteTitle),
	'preheader' => 'Confirm the change of your account e-mail address.',
	'intro'     => 'You have received this e-mail because someone has filled in the "change e-mail" form after logging into your account. If you are the one who requested this action, use the button below to proceed with the e-mail change.',
	'details'   => array(
		'Account'    => '{AccountUsername}',
		'Old e-mail' => '{OldEmail}',
		'New e-mail' => '{NewEmail}',
	),
	'button'    => array('Change e-mail', '{ChangeLink}'),
	'note'      => 'If you did not request this, you can ignore this e-mail.',
));
