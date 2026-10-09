<?php
if (!defined('FLUX_ROOT')) exit;
require_once 'Flux/MailLayout.php';

$siteTitle = Flux::config('SiteTitle');

echo Flux_MailLayout::render(array(
	'title'     => sprintf('%s: Password Has Been Reset', $siteTitle),
	'preheader' => 'Your new password is inside.',
	'intro'     => 'Here are the details outlining your new password.',
	'details'   => array(
		'Account'      => '{AccountUsername}',
		'New password' => '<span style="font-family:Consolas,Menlo,monospace;">{NewPassword}</span>',
	),
	'note'      => 'For your safety, log in and change this password as soon as possible.',
));
