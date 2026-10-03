<?php
if (!defined('FLUX_ROOT')) exit;
$markdownURL = 'https://daringfireball.net/projects/markdown/syntax';
?>
<h2><?php echo htmlspecialchars(Flux::message('MailerHeading')) ?></h2>
<?php if (!empty($errorMessage)): ?>
<p class="red"><?php echo htmlspecialchars((string)$errorMessage) ?></p>
<?php else: ?>
<p><?php echo htmlspecialchars(Flux::message('MailerInfo')) ?></p>
<?php endif ?>
<form action="<?php echo htmlspecialchars((string)$this->urlWithQs) ?>" method="post" name="mailerform" class="generic-form form-stack">
	<input type="hidden" name="_preview" value="0" />
	<?php echo Flux_Security::csrfGenerate('Mailer', true) ?>

	<div class="form-row">
		<span class="form-label"><?php echo htmlspecialchars(Flux::message('MailerFromLabel')) ?></span>
		<div class="form-static">
			<strong><?php echo htmlspecialchars((string)Flux::config('MailerFromName')) ?></strong>
			<span class="form-muted">&lt;<?php echo htmlspecialchars((string)Flux::config('MailerFromAddress')) ?>&gt;</span>
		</div>
	</div>

	<div class="form-row">
		<label for="to"><?php echo htmlspecialchars(Flux::message('MailerToLabel')) ?></label>
		<input type="text" name="to" id="to" value="<?php echo htmlspecialchars((string)$params->get('to')) ?>" />
	</div>

	<div class="form-row">
		<label for="subject"><?php echo htmlspecialchars(Flux::message('MailerSubjectLabel')) ?></label>
		<input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars((string)$params->get('subject')) ?>" />
	</div>

	<div class="form-row">
		<label for="body"><?php echo htmlspecialchars(Flux::message('MailerBodyLabel')) ?></label>
		<textarea name="body" id="body" rows="10"><?php echo htmlspecialchars((string)$params->get('body')) ?></textarea>
		<small class="field-hint">
			<?php echo htmlspecialchars(Flux::message('MailerBodyInfo')) ?>
			<a href="<?php echo $markdownURL ?>" target="_blank" rel="noopener">Markdown syntax guide</a>
		</small>
	</div>

	<div class="form-actions">
		<input type="submit" value="Send E-mail" class="btn-primary" />
		<input type="button" value="Preview" onclick="document.mailerform._preview.value = 1; document.mailerform.submit()" />
	</div>
</form>
<?php if ($preview): ?>
<h3>Preview</h3>
<div class="generic-form-div">
<?php echo $preview ?>
</div>
<?php endif ?>