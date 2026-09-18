<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('MvpLogTitle');

$sql = "SELECT COUNT(mvp_id) AS total FROM {$server->logsDatabase}.mvplog";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'mvp_date' => 'desc', 'kill_char_id', 'monster_id', 'prize', 'mvpexp', 'map'
));

$col = "mvp_id, mvp_date, kill_char_id, monster_id, prize, mvpexp, map";
$sql = $paginator->getSQL("SELECT $col FROM {$server->logsDatabase}.mvplog");
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$mvpKills = $sth->fetchAll();

if ($mvpKills) {
	$charIDs = array();
	$mobIDs  = array();

	foreach ($mvpKills as $mvpKill) {
		$charIDs[$mvpKill->kill_char_id] = null;
		$mobIDs[$mvpKill->monster_id]    = null;
	}

	if ($charIDs) {
		$ids = array_keys($charIDs);
		$sql = "SELECT char_id, name FROM {$server->charMapDatabase}.`char` WHERE char_id IN (".implode(',', array_fill(0, count($ids), '?')).")";
		$sth = $server->connection->getStatement($sql);
		$sth->execute($ids);

		$ids = $sth->fetchAll();

		// Map char_id to name.
		foreach ($ids as $id) {
			$charIDs[$id->char_id] = $id->name;
		}
	}

	require_once 'Flux/TemporaryTable.php';

	if ($mobIDs) {
		$mobDB      = "{$server->charMapDatabase}.monsters";
		$fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
		$tempMobs   = new Flux_TemporaryTable($server->connection, $mobDB, $fromTables);

		$ids = array_keys($mobIDs);
		$sql = "SELECT ID, iName FROM {$server->charMapDatabase}.monsters WHERE ID IN (".implode(',', array_fill(0, count($ids), '?')).")";
		$sth = $server->connection->getStatement($sql);
		$sth->execute($ids);

		$ids = $sth->fetchAll();

		// Map id to name.
		foreach ($ids as $id) {
			$mobIDs[$id->ID] = $id->iName;
		}
	}

	foreach ($mvpKills as $mvpKill) {
		if (array_key_exists($mvpKill->kill_char_id, $charIDs)) {
			$mvpKill->char_name = $charIDs[$mvpKill->kill_char_id];
		}
		if (array_key_exists($mvpKill->monster_id, $mobIDs)) {
			$mvpKill->monster_name = $mobIDs[$mvpKill->monster_id];
		}
	}
}
?>
