<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Castles';

$castleNames = Flux::castleNames();
$ids = implode(',', array_fill(0, count($castleNames), '?'));

$sql  = "SELECT castles.castle_id, castles.guild_id, guild.name AS guild_name, guild.emblem_len FROM {$server->charMapDatabase}.guild_castle AS castles ";
$sql .= "LEFT JOIN guild ON guild.guild_id = castles.guild_id ";
$sql .= "WHERE castles.castle_id IN ($ids)";
$sql .= "ORDER BY castles.castle_id ASC";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array_keys($castleNames));

$castles = $sth->fetchAll();

// Castles that are being fought over right now (empty when no WoE window is running).
$woeState         = $server->getWoeStatus();
$woeActiveCastles = array();
$woeEndsAt        = '';
if ($woeState['active'] && $woeState['window']) {
	$woeActiveCastles = array_map('intval', $woeState['window']['castles']);
	$woeEndsAt        = $woeState['window']['end']->format('l H:i');
}

?>