<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Barter Shops';

require_once 'Flux/TemporaryTable.php';

$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$npcName = $params->get('npc');

$sqlpartial = '';
$bind       = array();
if ($npcName) {
	$sqlpartial = 'WHERE name = ? ';
	$bind[]     = $npcName;
}

$col  = "name, itemId, amount, priceId, priceAmount, NULL AS zeny, ";
$col .= "NULL AS currencyId1, NULL AS currencyAmount1, NULL AS currencyRefine1, 'simple' AS barter_type";
$sql  = "SELECT $col FROM {$server->charMapDatabase}.npc_barter_data $sqlpartial ";

$col2  = "name, itemId, amount, NULL AS priceId, NULL AS priceAmount, zeny, ";
$col2 .= "currencyId1, currencyAmount1, currencyRefine1, 'expanded' AS barter_type";
$sql  .= "UNION ALL SELECT $col2 FROM {$server->charMapDatabase}.npc_expanded_barter_data $sqlpartial ";
$sql  .= "ORDER BY name ASC";

$sth = $server->connection->getStatement($sql);
$sth->execute(array_merge($bind, $bind));

$barters = $sth->fetchAll();

if ($barters) {
	$itemIDs = array();
	foreach ($barters as $barter) {
		$itemIDs[$barter->itemId]     = null;
		if ($barter->priceId) {
			$itemIDs[$barter->priceId] = null;
		}
		if ($barter->currencyId1) {
			$itemIDs[$barter->currencyId1] = null;
		}
	}

	$ids = array_keys($itemIDs);
	$sql = "SELECT id, name_japanese FROM {$server->charMapDatabase}.items WHERE id IN (".implode(',', array_fill(0, count($ids), '?')).")";
	$sth = $server->connection->getStatement($sql);
	$sth->execute($ids);

	foreach ($sth->fetchAll() as $item) {
		$itemIDs[$item->id] = $item->name_japanese;
	}

	foreach ($barters as $barter) {
		$barter->item_name = array_key_exists($barter->itemId, $itemIDs) ? $itemIDs[$barter->itemId] : null;

		if ($barter->priceId) {
			$barter->price_item_name = array_key_exists($barter->priceId, $itemIDs) ? $itemIDs[$barter->priceId] : null;
		}
		if ($barter->currencyId1) {
			$barter->currency_item_name = array_key_exists($barter->currencyId1, $itemIDs) ? $itemIDs[$barter->currencyId1] : null;
		}
	}
}
?>
