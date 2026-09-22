<?php
if (!defined('FLUX_ROOT')) exit;

require_once 'Flux/Config.php';

$title = 'Add Item';

$itemID        = $params->get('item_id');
$viewID        = $params->get('view');
$type          = $params->get('type');
$identifier    = $params->get('name_english');
$itemName      = $params->get('name_japanese');
$slots         = $params->get('slots');
$npcBuy        = $params->get('npc_buy');
$npcSell       = $params->get('npc_sell');
$weight        = $params->get('weight');
$atk           = $params->get('atk');
$matk          = $params->get('matk');
$defense       = $params->get('defense');
$range         = $params->get('range');
$weaponLevel   = $params->get('weapon_level');
$equipLevelMin = $params->get('equip_level_min');
$equipLevelMax = $params->get('equip_level_max');
$refineable    = $params->get('refineable');
$equipLocs     = $params->get('equip_locations');
$equipUpper    = $params->get('equip_upper');
$equipJobs     = $params->get('equip_jobs');
$equipMale     = $params->get('equip_male');
$equipFemale   = $params->get('equip_female');
$script        = $params->get('script');
$equipScript   = $params->get('equip_script');
$unequipScript = $params->get('unequip_script');

// NPC Buy/Sell is defaulted to twice/half the sell/buy price, or a zero value if both are null.
if (is_null($npcBuy) && is_null($npcSell)) {
	$npcBuy = 0;
	$npcSell = 0;
}
else if (is_null($npcBuy)) {
	$npcBuy = $npcSell * 2;
}
else if (is_null($npcSell)) {
	$npcSell = $npcBuy / 2;
}

// Weight is defaulted to a zero value.
if (is_null($weight)) {
	$weight = 0;
}

// ATK is defaulted to a zero value.
if (is_null($atk)) {
	$atk = 0;
}

// MATK is defaulted to a zero value.
if (is_null($matk)) {
	$matk = 0;
}

// Defence is defaulted to a zero value.
if (is_null($defense)) {
	$defense = 0;
}

// Range is defaulted to a zero value.
if (is_null($range)) {
	$range = 0;
}

// Slots is defaulted to a zero value.
if (is_null($slots)) {
	$slots = 0;
}

// Equip Jobs is defaulted to "All Jobs".
if (is_null($equipJobs)) {
	$equipJobs = 0xffffffff;
}

// Equip Upper is defaulted to "No Restrictions".
if (is_null($equipJobs)) {
	$equipJobs = 0x63;
}

// Equip Location is defaulted to a zero value.
if (is_null($equipLocs)) {
	$equipLocs = 0;
}

// Weapon Level is defaulted to a zero value.
if (is_null($weaponLevel)) {
	$weaponLevel = 0;
}

// Equip Level Minimum is defaulted to a zero value.
if (is_null($equipLevelMin)) {
	$equipLevelMin = 0;
}

// Equip Level Maximum is defaulted to server default.
if (is_null($equipLevelMax)) {
	$equipLevelMax = $server->maxBaseLevel;
}

// Refineable is defaulted to true for types 4 or 5.
if (is_null($refineable) && ($type == 4 || $type == 5)) {
	$refineable = 1;
}

// View ID is defaulted to a zero value.
if (is_null($viewID)) {
	$viewID = 0;
}

// Script is defaulted to blank text.
if (is_null($script)) {
	$script = '';
}

// Equip Script is defaulted to blank text.
if (is_null($equipScript)) {
	$equipScript = '';
}

// Unequip Script is defaulted to blank text.
if (is_null($unequipScript)) {
	$unequipScript = '';
}

if (count($_POST) && $params->get('additem')) {
	// Equip locations.
	if ($equipLocs instanceOf Flux_Config) {
		$equipLocs = $equipLocs->toArray();
	}

	// Equip upper.
	if ($equipUpper instanceOf Flux_Config) {
		$equipUpper = $equipUpper->toArray();
	}

	// Equip jobs.
	if ($equipJobs instanceOf Flux_Config) {
		$equipJobs = $equipJobs->toArray();
	}
	
	// Sanitize to NULL
	$nullables = array(
		'viewID', 'slots', 'npcBuy', 'npcSell', 'weight', 'atk', 'matk', 'defense', 'range',
		'weaponLevel', 'equipLevelMin', 'equipLevelMax', 'script', 'equipScript', 'unequipScript'
	);
	
	foreach ($nullables as $nullable) {
		if (trim($$nullable) == '') {
			$$nullable = null;
		}
	}

	// Refineable should be 1 or 0 if it's not null.
	if (!is_null($refineable)) {
		$refineable = intval((bool)$refineable);
	}

	if (!$itemID) {
		$errorMessage = 'You must specify an item ID.';
	}
	elseif (!ctype_digit($itemID)) {
		$errorMessage = 'Item ID must be a number.';
	}
	elseif (!is_null($viewID) && !ctype_digit($viewID)) {
		$errorMessage = 'View ID must be a number.';
	}
	elseif (!$identifier) {
		$errorMessage = 'You must specify an identifier.';
	}
	elseif (!$itemName) {
		$errorMessage = 'You must specify an item name.';
	}
	elseif (!is_null($slots) && !ctype_digit($slots)) {
		$errorMessage = 'Slots must be a number.';
	}
	elseif (!is_null($npcBuy) && !ctype_digit($npcBuy)) {
		$errorMessage = 'NPC buying price must be a number.';
	}
	elseif (!is_null($npcSell) && !ctype_digit($npcSell)) {
		$errorMessage = 'NPC selling price must be a number.';
	}
	elseif (!is_null($weight) && !ctype_digit($weight)) {
		$errorMessage = 'Weight must be a number.';
	}
	elseif (!is_null($atk) && !ctype_digit($atk)) {
		$errorMessage = 'ATK must be a number.';
	}
	elseif (!is_null($matk) && !ctype_digit($matk)) {
		$errorMessage = 'MATK must be a number.';
	}
	elseif (!is_null($defense) && !ctype_digit($defense)) {
		$errorMessage = 'Defense must be a number.';
	}
	elseif (!is_null($range) && !ctype_digit($range)) {
		$errorMessage = 'Range must be a number.';
	}
	elseif (!is_null($weaponLevel) && !ctype_digit($weaponLevel)) {
		$errorMessage = 'Weapon level must be a number.';
	}
	elseif (!is_null($equipLevelMin) && !ctype_digit($equipLevelMin)) {
		$errorMessage = 'Minimum equip level must be a number.';
	}
	elseif (!is_null($equipLevelMax) && !ctype_digit($equipLevelMax)) {
		$errorMessage = 'Maximum equip level must be a number.';
	}
	elseif ( !Flux_Security::csrfValidate('ItemAdd', $_POST, $error) ) {
		$errorMessage = $error;
	}
	else {
		if (empty($errorMessage) && is_array($equipLocs)) {
			$locs = Flux::getEquipLocationList();
			foreach ($equipLocs as $bit) {
				if (!array_key_exists($bit, $locs)) {
					$errorMessage = 'Invalid equip location specified.';
					$equipLocs = null;
					break;
				}
			}
		}
		if (empty($errorMessage) && is_array($equipUpper)) {
			$upper = Flux::getEquipUpperList();
			foreach ($equipUpper as $bit) {
				if (!array_key_exists($bit, $upper)) {
					$errorMessage = 'Invalid equip upper specified.';
					$equipUpper = null;
					break;
				}
			}
		}
		if (empty($errorMessage) && is_array($equipJobs)) {
			$jobs = Flux::getEquipJobsList();
			foreach ($equipJobs as $bit) {
				if (!array_key_exists($bit, $jobs)) {
					$errorMessage = 'Invalid equippable job specified.';
					$equipJobs = null;
					break;
				}
			}
		}
		if (empty($errorMessage)) {
			require_once 'Flux/TemporaryTable.php';

			$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
			$tableName = "{$server->charMapDatabase}.items";
			$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);
			$shopTable = Flux::config('FluxTables.ItemShopTable');
			
			$sth = $server->connection->getStatement("SELECT id, name_japanese, origin_table FROM $tableName WHERE id = ? LIMIT 1");
			$sth->execute(array($itemID));
			
			$item = $sth->fetch();
			if ($item && $item->id) {
				$errorMessage = sprintf('An item already exists with that ID (%s, %s, #%d).', $item->name_japanese, $item->origin_table, $item->id);
			}
			else {
				$cols = array('id', 'name_english', 'name_japanese', 'type', 'weight');
				$bind = array($itemID, $identifier, $itemName, $type, $weight*10);
				$vals = array(
					'view_sprite'           => $viewID,
					'slots'          => $slots,
					'price_buy'      => $npcBuy,
					'price_sell'     => $npcSell,
					'atk'            => $atk,
					'matk'           => $matk,
					'defence'        => $defense,
					'`range`'        => $range,
					'weapon_level'   => $weaponLevel,
					'equip_level_min'=> $equipLevelMin,
					'equip_level_max'=> $equipLevelMax,
					'script'         => $script,
					'equip_script'   => $equipScript,
					'unequip_script' => $unequipScript,
					'refineable'     => $refineable
				);
				
				foreach ($vals as $col => $val) {
					if (!is_null($val)) {
						$cols[] = $col;
						$bind[] = $val;
					}
				}
				
				if ($equipLocs) {
					$bits = 0;
					foreach ($equipLocs as $bit) {
						$bits |= $bit;
					}
					$cols[] = 'equip_locations';
					$bind[] = $bits;
				}
				
				if ($equipUpper) {
					$bits = 0;
					foreach ($equipUpper as $bit) {
						$bits |= $bit;
					}
					$cols[] = 'equip_upper';
					$bind[] = $bits;
				}
				
				if ($equipJobs) {
					$bits = 0;
					foreach ($equipJobs as $bit) {
						$bits |= $bit;
					}
					$cols[] = 'equip_jobs';
					$bind[] = $bits;
				}
				
				$gender = null;
				if ($equipMale && $equipFemale) {
					$gender = 2;
				}
				elseif ($equipMale) {
					$gender = 1;
				}
				elseif ($equipFemale) {
					$gender = 0;
				}
				
				if (!is_null($gender)) {
					$cols[] = 'equip_genders';
					$bind[] = $gender;
				}
				
				$sql  = "INSERT INTO {$server->charMapDatabase}.item_db2 (".implode(', ', $cols).") ";
				$sql .= "VALUES (".implode(', ', array_fill(0, count($bind), '?')).")";
				$sth  = $server->connection->getStatement($sql);
				
				if ($sth->execute($bind)) {
					$session->setMessageData("Your item '$itemName' ($itemID) has been successfully added!");
					
					if ($auth->actionAllowed('item', 'view')) {
						$this->redirect($this->url('item', 'view', array('id' => $itemID)));
					}
					else {
						$this->redirect();
					}
				}
				else {
					$errorMessage = 'Failed to add item!';
				}
			}
		}
	}
}

if (!is_array($equipLocs)) {
	$equipLocs = array();
}
if (!is_array($equipUpper)) {
	$equipUpper = array();
}
if (!is_array($equipJobs)) {
	$equipJobs = array();
}
?>
