<?php
if (!defined('FLUX_ROOT')) exit;
require_once dirname(__FILE__) . '/_layout.php';

$siteTitle = Flux::config('SiteTitle');

echo fluxEmailLayout(array(
	'title'     => sprintf('%s: Reset Password', $siteTitle),
	'preheader' => 'Use the link inside to choose a new password.',
	'intro'     => 'You have received this e-mail because you or someone else has filled in our "reset password" form, requesting to reset the password of your account on our server.',
	'details'   => array('Account' => '{AccountUsername}'),
	'button'    => array('Reset password', '{ResetLink}'),
	'note'      => 'If you did not request this, you can ignore this e-mail and your password will stay the same.',
));
