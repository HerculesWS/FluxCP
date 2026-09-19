<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('MvpLogHeading')) ?></h2>
<?php if ($mvpKills): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('mvp_date', Flux::message('MvpLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('kill_char_id', Flux::message('MvpLogCharacterLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('monster_id', Flux::message('MvpLogMonsterLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('prize', Flux::message('MvpLogPrizeLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('mvpexp', Flux::message('MvpLogExpLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('map', Flux::message('MvpLogMapLabel')) ?></th>
	</tr>
	<?php foreach ($mvpKills as $mvpKill): ?>
	<tr>
		<td align="right"><?php echo $this->formatDateTime($mvpKill->mvp_date) ?></td>
		<td>
			<?php if ($mvpKill->char_name): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<strong><?php echo $this->linkToCharacter($mvpKill->kill_char_id, $mvpKill->char_name) ?></strong>
				<?php else: ?>
					<strong><?php echo htmlspecialchars($mvpKill->char_name) ?></strong>
				<?php endif ?>
			<?php elseif ($mvpKill->kill_char_id): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<strong><?php echo $this->linkToCharacter($mvpKill->kill_char_id, $mvpKill->kill_char_id) ?></strong>
				<?php else: ?>
					<strong><?php echo htmlspecialchars($mvpKill->kill_char_id) ?></strong>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($mvpKill->monster_name): ?>
				<?php if ($auth->actionAllowed('monster', 'view')): ?>
					<em><?php echo $this->linkToMonster($mvpKill->monster_id, $mvpKill->monster_name) ?></em>
				<?php else: ?>
					<em><?php echo htmlspecialchars($mvpKill->monster_name) ?></em>
				<?php endif ?>
			<?php elseif ($mvpKill->monster_id): ?>
				<?php if ($auth->actionAllowed('monster', 'view')): ?>
					<em><?php echo $this->linkToMonster($mvpKill->monster_id, $mvpKill->monster_id) ?></em>
				<?php else: ?>
					<em><?php echo htmlspecialchars($mvpKill->monster_id) ?></em>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td><?php echo number_format((int)$mvpKill->prize) ?></td>
		<td><?php echo number_format((int)$mvpKill->mvpexp) ?></td>
		<td>
			<?php if ($mvpKill->map): ?>
				<?php echo htmlspecialchars(basename($mvpKill->map, '.gat')) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('MvpLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
