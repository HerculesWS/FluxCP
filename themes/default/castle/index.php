<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Castles</h2>
<p>This page shows what castles are activated and which guilds own them.</p>
<?php if ($castles): ?>
<?php
// Group castles by the configured regions; anything unassigned goes under "Other".
$regionConfig = Flux::config('CastleRegions');
$regionList   = $regionConfig ? $regionConfig->toArray() : array();
$castleById   = array();
foreach ($castles as $castle) {
	$castleById[$castle->castle_id] = $castle;
}
$regions = array();
$placed  = array();
foreach ($regionList as $regionName => $regionIds) {
	$members = array();
	foreach ((array)$regionIds as $regionId) {
		if (isset($castleById[$regionId])) {
			$members[] = $castleById[$regionId];
			$placed[$regionId] = true;
		}
	}
	if ($members) {
		$regions[$regionName] = $members;
	}
}
$unassigned = array();
foreach ($castles as $castle) {
	if (!isset($placed[$castle->castle_id])) {
		$unassigned[] = $castle;
	}
}
if ($unassigned) {
	$regions[$regions ? 'Other' : 'Castles'] = $unassigned;
}
?>
<?php if ($woeState['active']): ?>
<div class="alert-gold" role="status">
	<strong>War of Emperium is Active</strong>
	<span>
		<?php if ($woeEndsAt): ?>Ends <?php echo htmlspecialchars($woeEndsAt) ?>.<?php endif ?>
		<?php if ($woeActiveCastles): ?>The highlighted castles are being contested.<?php endif ?>
	</span>
</div>
<?php endif ?>
<div class="castle-regions">
	<?php foreach ($regions as $regionName => $regionCastles): ?>
	<?php $held = 0; $contested = 0; foreach ($regionCastles as $regionCastle) { if ($regionCastle->guild_name) ++$held; if (in_array((int)$regionCastle->castle_id, $woeActiveCastles, true)) ++$contested; } ?>
	<details class="castle-region" open>
		<summary class="castle-region-title">
			<span class="castle-region-name"><?php echo htmlspecialchars((string)$regionName) ?></span>
			<?php if ($contested): ?><span class="castle-region-woe"><?php echo (int)$contested ?> in WoE</span><?php endif ?>
			<span class="castle-region-held"><?php echo (int)$held ?>/<?php echo count($regionCastles) ?> held</span>
		</summary>
		<ul class="castle-list">
			<?php foreach ($regionCastles as $castle): ?>
			<?php $inWoe = in_array((int)$castle->castle_id, $woeActiveCastles, true) ?>
			<li class="castle-item<?php if ($inWoe) echo ' castle-woe' ?>">
				<span class="castle-name"><?php echo htmlspecialchars((string)$castleNames[$castle->castle_id]) ?><?php if ($inWoe): ?> <span class="castle-woe-badge">WoE</span><?php endif ?></span>
				<span class="castle-guild">
					<?php if ($castle->guild_name): ?>
						<?php if ($castle->emblem_len): ?>
							<img src="<?php echo $this->emblem($castle->guild_id) ?>" alt="" class="castle-emblem" />
						<?php endif ?>
						<?php if ($castle->emblem_len && $auth->actionAllowed('guild', 'view') && $auth->allowedToViewGuild): ?>
							<?php echo $this->linkToGuild($castle->guild_id, $castle->guild_name) ?>
						<?php elseif (!$castle->emblem_len && $auth->actionAllowed('guild', 'view') && $auth->allowedToViewGuild): ?>
							<?php echo $this->linkToGuild($castle->guild_id, $castle->guild_name) ?>
						<?php else: ?>
							<?php echo htmlspecialchars((string)$castle->guild_name) ?>
						<?php endif ?>
					<?php else: ?>
						<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
					<?php endif ?>
				</span>
			</li>
			<?php endforeach ?>
		</ul>
	</details>
	<?php endforeach ?>
</div>
<script>
// On phones the regions start collapsed so the page stays short.
document.addEventListener('DOMContentLoaded', function () {
	if (window.matchMedia && window.matchMedia('(max-width: 600px)').matches) {
		var regions = document.querySelectorAll('.castle-region');
		for (var i = 0; i < regions.length; ++i) {
			// Keep open any region that has a castle in an active WoE window.
			regions[i].open = regions[i].querySelector('.castle-woe') !== null;
		}
	}
});
</script>
<?php else: ?>
<p>No castles found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>