<?php
if (!defined('FLUX_ROOT')) exit;

include 'status.php';

$groups = array();

foreach ($serverStatus as $privServerName => $gameServers) {
	$servers = array();
	
	foreach ($gameServers as $serverName => $gameServer) {
		$server = array(
			'name'               => (string)$serverName,
			'loginServer'        => (bool)$gameServer['loginServerUp'],
			'charServer'         => (bool)$gameServer['charServerUp'],
			'mapServer'          => (bool)$gameServer['mapServerUp'],
			'playersOnline'      => (int)$gameServer['playersOnline'],
			'autotradeMerchants' => (int)$gameServer['autotradeMerchants'],
			'population'         => (int)$gameServer['population']
		);
		
		// Highest Players Online value recorded, with the time it was reached (ISO 8601).
		if (isset($serverPeak[$privServerName][$serverName])) {
			$peak = $serverPeak[$privServerName][$serverName];
			$server['peakPlayersOnline'] = (int)$peak['peak'];
			$server['peakReachedAt']     = $peak['time'] ? date('c', (int)$peak['time']) : null;
		}
		
		// War of Emperium state: in progress now, or the next scheduled window.
		if (isset($woeStatus[$privServerName][$serverName]) && $woeStatus[$privServerName][$serverName]['configured']) {
			$woe = $woeStatus[$privServerName][$serverName];
			$server['woe'] = array(
				'active'   => (bool)$woe['active'],
				'start'    => $woe['start'] ?: null,
				'end'      => $woe['end'] ?: null,
				'startsIn' => (!$woe['active'] && $woe['in']) ? $woe['in'] : null,
				'castles'  => array_values($woe['castles'])
			);
		}
		
		$servers[] = $server;
	}
	
	$groups[] = array('name' => (string)$privServerName, 'servers' => $servers);
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(array('serverStatus' => $groups), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit;
?>
