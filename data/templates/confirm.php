<?php
if (!defined('FLUX_ROOT')) exit;
require_once 'Flux/MailLayout.php';

$siteTitle = Flux::config('SiteTitle');
$expire    = Flux::config('EmailConfirmExpire');

echo Flux_MailLayout::render(array(
	'title'     => sprintf('%s: Account Confirmation', $siteTitle),
	'preheader' => 'Activate your account to finish signing up.',
	'intro'     => 'You have received this e-mail because you or someone else has created an account with <strong>' . htmlspecialchars((string)$siteTitle) . '</strong> using this e-mail address. Use the button below to activate the account.',
	'details'   => array('Account' => '{AccountUsername}'),
	'button'    => array('Activate account', '{ConfirmationLink}'),
	'note'      => $expire ? 'All unconfirmed accounts will be deleted from our system within ' . (int)$expire . ' hour(s) of registration.' : '',
));
