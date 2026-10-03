<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Map Statistics</h2>
<?php if ($maps): ?>
<?php $playerTotal = 0; foreach ($maps as $map) $playerTotal += $map->player_count ?>
<p>This page shows how many online players are located a specific map, for all maps that have <em>any</em> online players at all.</p>
<?php
$sortedMaps = $maps;
usort($sortedMaps, function ($a, $b) {
	return $b->player_count <=> $a->player_count ?: strcmp((string)$a->map_name, (string)$b->map_name);
});
$maxCount = max(1, (int)$sortedMaps[0]->player_count);
?>
<div class="info-tiles mapstat-summary">
	<div class="info-tile">
		<span class="info-tile-label">Online player(s) found</span>
		<span class="info-tile-value"><?php echo number_format($playerTotal) ?></span>
	</div>
	<div class="info-tile">
		<span class="info-tile-label">Map(s) with players</span>
		<span class="info-tile-value"><?php echo number_format(count($maps)) ?></span>
	</div>
</div>
<ul class="map-list">
	<?php foreach ($sortedMaps as $map): ?>
	<li class="map-item">
		<span class="map-name"><?php echo htmlspecialchars(basename($map->map_name, '.gat')) ?></span>
		<span class="map-bar"><span class="map-bar-fill" style="width: <?php echo round(($map->player_count / $maxCount) * 100) ?>%"></span></span>
		<span class="map-count"><strong><?php echo number_format($map->player_count) ?></strong> player(s)</span>
	</li>
	<?php endforeach ?>
</ul>
<?php else: ?>
<p>No players found on any maps. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>