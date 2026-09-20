<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('NpcLogHeading')) ?></h2>
<?php if ($npcLogs): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('npc_date', Flux::message('NpcLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('account_id', Flux::message('NpcLogAccountLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_id', Flux::message('NpcLogCharacterLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('map', Flux::message('NpcLogMapLabel')) ?></th>
		<th><?php echo Flux::message('NpcLogMessageLabel') ?></th>
	</tr>
	<?php foreach ($npcLogs as $npcLog): ?>
	<tr>
		<td align="right"><?php echo $this->formatDateTime($npcLog->npc_date) ?></td>
		<td>
			<?php if ($npcLog->account_id): ?>
				<?php if ($auth->actionAllowed('account', 'view')): ?>
					<?php echo $this->linkToAccount($npcLog->account_id, $npcLog->account_id) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($npcLog->account_id) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($npcLog->char_name): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<strong><?php echo $this->linkToCharacter($npcLog->char_id, $npcLog->char_name) ?></strong>
				<?php else: ?>
					<strong><?php echo htmlspecialchars($npcLog->char_name) ?></strong>
				<?php endif ?>
			<?php elseif ($npcLog->char_id): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<strong><?php echo $this->linkToCharacter($npcLog->char_id, $npcLog->char_id) ?></strong>
				<?php else: ?>
					<strong><?php echo htmlspecialchars($npcLog->char_id) ?></strong>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($npcLog->map): ?>
				<?php echo htmlspecialchars(basename($npcLog->map, '.gat')) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($npcLog->mes): ?>
				<?php echo htmlspecialchars($npcLog->mes) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('NpcLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
