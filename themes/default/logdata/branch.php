<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('BranchLogHeading')) ?></h2>
<?php if ($branches): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('branch_date', Flux::message('BranchLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('account_id', Flux::message('BranchLogAccountLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_id', Flux::message('BranchLogCharacterLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('map', Flux::message('BranchLogMapLabel')) ?></th>
	</tr>
	<?php foreach ($branches as $branch): ?>
	<tr>
		<td align="right"><?php echo $this->formatDateTime($branch->branch_date) ?></td>
		<td>
			<?php if ($branch->account_id): ?>
				<?php if ($auth->actionAllowed('account', 'view')): ?>
					<?php echo $this->linkToAccount($branch->account_id, $branch->account_id) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($branch->account_id) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($branch->char_name): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<strong><?php echo $this->linkToCharacter($branch->char_id, $branch->char_name) ?></strong>
				<?php else: ?>
					<strong><?php echo htmlspecialchars($branch->char_name) ?></strong>
				<?php endif ?>
			<?php elseif ($branch->char_id): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<strong><?php echo $this->linkToCharacter($branch->char_id, $branch->char_id) ?></strong>
				<?php else: ?>
					<strong><?php echo htmlspecialchars($branch->char_id) ?></strong>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($branch->map): ?>
				<?php echo htmlspecialchars(basename($branch->map, '.gat')) ?>
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
	<?php echo htmlspecialchars(Flux::message('BranchLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
