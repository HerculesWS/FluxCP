<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Vending Shops';

require_once 'Flux/TemporaryTable.php';

$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$charName = $params->get('char_name');

$sqlpartial = '';
$bind       = array();
if ($charName) {
	$sqlpartial = 'WHERE ch.name LIKE ? ';
	$bind[]     = "%$charName%";
}

$col  = "am.account_id, am.char_id, am.title, ch.name AS char_name, ch.last_map, ch.last_x, ch.last_y, ";
$col .= "ad.itemkey, ad.amount, ad.price, ";
$col .= "ci.nameid, ci.refine, ci.card0, ci.card1, ci.card2, ci.card3, items.name_japanese AS item_name";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.autotrade_merchants AS am ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS ch ON ch.char_id = am.char_id ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.autotrade_data AS ad ON ad.char_id = am.char_id ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.cart_inventory AS ci ON ci.id = ad.itemkey ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.items ON items.id = ci.nameid ";
$sql .= "$sqlpartial";
$sql .= "ORDER BY am.char_id ASC, ad.itemkey ASC";

$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$rows = $sth->fetchAll();

$vendors = array();
foreach ($rows as $row) {
	if (!array_key_exists($row->char_id, $vendors)) {
		$vendors[$row->char_id] = array(
			'account_id' => $row->account_id,
			'char_id'    => $row->char_id,
			'char_name'  => $row->char_name,
			'title'      => $row->title,
			'map'        => $row->last_map ? basename($row->last_map, '.gat') : null,
			'x'          => $row->last_x,
			'y'          => $row->last_y,
			'items'      => array(),
		);
	}

	if ($row->itemkey) {
		$vendors[$row->char_id]['items'][] = $row;
	}
}
?>
