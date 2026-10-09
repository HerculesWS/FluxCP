<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Guild Ranking</h2>
<h3>
	Top <?php echo number_format($limit=(int)Flux::config('GuildRankingLimit')) ?> Guilds
	on <?php echo htmlspecialchars((string)$server->serverName) ?>
</h3>
<?php if ($guilds): ?>
	<table class="horizontal-table">
		<tr>
			<th>Rank</th>
			<th colspan="2">Guild Name</th>
			<th>Guild Level</th>
			<th>Castles Owned</th>
			<th>Members</th>
			<th>Average Level</th>
			<th>Experience</th>
		</tr>
		<?php for ($i = 0, $shown = min((int)$limit, count($guilds)); $i < $shown; ++$i): ?>
		<tr<?php if (!isset($guilds[$i])) echo ' class="empty-row"'; if ($i === 0) echo ' class="top-ranked" title="<strong>'.htmlspecialchars((string)$guilds[$i]->name).'</strong> is the top ranked guild!"' ?>>
			<td class="align-right"><?php echo number_format($i + 1) ?></td>
			<?php if (isset($guilds[$i])): ?>
			<?php if ($guilds[$i]->emblem_len): ?>
			<td class="emblem-cell"><img src="<?php echo $this->emblem($guilds[$i]->guild_id) ?>" alt="" /></td>
			<?php endif ?>
			<td<?php if (!$guilds[$i]->emblem_len) echo ' colspan="2"' ?>><strong>
				<?php if ($auth->actionAllowed('guild', 'view') && $auth->allowedToViewGuild): ?>
					<?php echo $this->linkToGuild($guilds[$i]->guild_id, $guilds[$i]->name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars((string)$guilds[$i]->name) ?>
				<?php endif ?>
			</strong></td>
			<td><?php echo number_format($guilds[$i]->guild_lv) ?></td>
			<td><?php echo number_format($guilds[$i]->castles) ?></td>
			<td><?php echo number_format($guilds[$i]->members) ?></td>
			<td><?php echo number_format($guilds[$i]->average_lv) ?></td>
			<td><?php echo number_format($guilds[$i]->exp) ?></td>
			<?php else: ?>
			<td colspan="8"></td>
			<?php endif ?>
		</tr>
		<?php endfor ?>
	</table>
<?php else: ?>
<p>No guilds found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>