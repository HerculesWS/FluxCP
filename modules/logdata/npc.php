<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('NpcLogTitle');

$sql = "SELECT COUNT(npc_id) AS total FROM {$server->logsDatabase}.npclog";
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'npc_date' => 'desc', 'account_id', 'char_id', 'char_name', 'map'
));

$col = "npc_id, npc_date, account_id, char_id, char_name, map, mes";
$sql = $paginator->getSQL("SELECT $col FROM {$server->logsDatabase}.npclog");
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute();

$npcLogs = $sth->fetchAll();
?>
