<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('MailerTitle');
$preview = '';

if (count($_POST)) {
	$prev    = (bool)$params->get('_preview');
	$to      = trim((string)$params->get('to'));
	$subject = trim((string)$params->get('subject'));
	$body    = trim((string)$params->get('body'));
	
	if (!$to) {
		$errorMessage = Flux::message('MailerEnterToAddress');
	}
	elseif (!$subject) {
		$errorMessage = Flux::message('MailerEnterSubject');
	}
	elseif (!$body) {
		$errorMessage = Flux::message('MailerEnterBodyText');
	}
	elseif ( !Flux_Security::csrfValidate('Mailer', $_POST, $error) ) {
		$errorMessage = $error;
	}

	if (empty($errorMessage)) {
		if ($prev) {
			require_once 'markdown/markdown.php';
			$preview = Markdown($body);
		}
		else {
			require_once 'Flux/Mailer.php';
			
			$mail = new Flux_Mailer();
			$opts = array('_ignoreTemplate' => true, '_useMarkdown' => true);
			
			if ($mail->send($to, $subject, $body, $opts)) {
				$session->setMessageData(sprintf(Flux::message('MailerEmailHasBeenSent'), $to));
				$this->redirect();
			}
			else {
				$errorMessage = Flux::message('MailerFailedToSend');
			}
		}
	}
}
?>