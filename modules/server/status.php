<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('ServerStatusTitle');
$cache = FLUX_DATA_DIR.'/tmp/ServerStatus.cache';

$serverStatus = null;
if (file_exists($cache) && (time() - filemtime($cache)) < (Flux::config('ServerStatusCache') * 60)) {
	$serverStatus = json_decode((string)file_get_contents($cache), true);
}

if (!is_array($serverStatus)) {
	$serverStatus = array();
	foreach (Flux::$loginAthenaGroupRegistry as $groupName => $loginAthenaGroup) {
		if (!array_key_exists($groupName, $serverStatus)) {
			$serverStatus[$groupName] = array();
		}

		$loginServerUp = $loginAthenaGroup->loginServer->isUp();

		foreach ($loginAthenaGroup->athenaServers as $athenaServer) {
			$serverName = $athenaServer->serverName;
			
			$sql = "SELECT COUNT(char_id) AS population FROM {$athenaServer->charMapDatabase}.char WHERE online > 0";
			$sth = $loginAthenaGroup->connection->getStatement($sql);
			$sth->execute();
			$res = $sth->fetch();
			$population = intval($res ? $res->population : 0);
			
			$sql = "SELECT COUNT(char_id) AS autotrade_merchants FROM {$athenaServer->charMapDatabase}.autotrade_merchants";
			$sth = $loginAthenaGroup->connection->getStatement($sql);
			$sth->execute();
			$res = $sth->fetch();
			$autotrade_merchants = intval($res ? $res->autotrade_merchants : 0);
			
			$serverStatus[$groupName][$serverName] = array(
				'loginServerUp' => $loginServerUp,
				 'charServerUp' => $athenaServer->charServer->isUp(),
				  'mapServerUp' => $athenaServer->mapServer->isUp(),
				'playersOnline' => $population - $autotrade_merchants,
				'autotradeMerchants' => $autotrade_merchants,
				'population' => $population
			);
		}
	}
	
	$fp = fopen($cache, 'w');
	if (is_resource($fp)) {
		fwrite($fp, json_encode($serverStatus));
		fclose($fp);
	}
}

// Peak of the Players Online count, kept in a small file so it survives the status cache refreshing.
$peakDir    = FLUX_DATA_DIR.'/logs/peak';
$serverPeak = array();
if (!is_dir($peakDir)) {
	@mkdir($peakDir, 0700, true);
}
foreach ($serverStatus as $groupName => $gameServers) {
	foreach ($gameServers as $serverName => $gameServer) {
		$peakFile = $peakDir.'/'.preg_replace('/[^A-Za-z0-9_.-]/', '_', "$groupName-$serverName").'.json';
		$peak     = array('peak' => 0, 'time' => 0);
		if (is_file($peakFile)) {
			$stored = json_decode((string)file_get_contents($peakFile), true);
			if (is_array($stored)) {
				$peak = array_merge($peak, $stored);
			}
		}
		$playersOnline = (int)$gameServer['playersOnline'];
		if (!is_file($peakFile) || $playersOnline > $peak['peak']) {
			$peak = array('peak' => $playersOnline, 'time' => time());
			@file_put_contents($peakFile, json_encode($peak));
		}
		$serverPeak[$groupName][$serverName] = $peak;
	}
}

// Live WoE state (never cached): in progress now, or the next scheduled window.
$castleNames = Flux::castleNames();
$woeStatus   = array();
foreach (Flux::$loginAthenaGroupRegistry as $groupName => $loginAthenaGroup) {
	foreach ($loginAthenaGroup->athenaServers as $athenaServer) {
		$state  = $athenaServer->getWoeStatus();
		$window = $state['window'];
		$info   = array('configured' => $state['configured'], 'active' => $state['active'], 'when' => '', 'until' => '', 'in' => '', 'start' => '', 'end' => '', 'castles' => array());
		
		if ($window) {
			$start = $window['start'];
			$end   = $window['end'];
			$info['when'] = $start->format('l H:i').' - '.($start->format('Y-m-d') === $end->format('Y-m-d') ? '' : $end->format('l ')).$end->format('H:i');
			
			$info['until'] = $end->format('l H:i');
			$info['start'] = $start->format('c');
			$info['end']   = $end->format('c');
			
			if (!$state['active']) {
				$secs  = max(0, $start->getTimestamp() - time());
				$days  = (int)floor($secs / 86400);
				$hours = (int)floor(($secs % 86400) / 3600);
				$mins  = (int)floor(($secs % 3600) / 60);
				$info['in'] = $days ? "{$days}d {$hours}h" : ($hours ? "{$hours}h {$mins}m" : "{$mins}m");
			}
			
			foreach ($window['castles'] as $castleId) {
				if (isset($castleNames[$castleId])) {
					$info['castles'][] = $castleNames[$castleId];
				}
			}
		}
		
		$woeStatus[$groupName][$athenaServer->serverName] = $info;
	}
}
?>