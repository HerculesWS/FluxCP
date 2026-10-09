<?php
if (!defined('FLUX_ROOT')) exit;

include 'status.php';
$dom  = new DomDocument('1.0', 'utf-8');
$root = $dom->createElement('ServerStatus'); // Root element.

foreach ($serverStatus as $privServerName => $gameServers) {
	$group  = $dom->createElement('Group');
	$name   = $dom->createAttribute('name');
	$name->nodeValue = $privServerName;
	
	// Append server name element.
	$group->appendChild($name);
	
	foreach ($gameServers as $serverName => $gameServer) {
		$serv = $dom->createElement('Server');
		$name = $dom->createAttribute('name');
		$name->nodeValue = $serverName;
		
		$serv->appendChild($name);
		
		$lserv  = $dom->createAttribute('loginServer');
		$cserv  = $dom->createAttribute('charServer');
		$mserv  = $dom->createAttribute('mapServer');
		$online = $dom->createAttribute('playersOnline');
		$atmerc = $dom->createAttribute('autotradeMerchants');
		$population = $dom->createAttribute('population');
		
		$lserv->nodeValue  = (int)$gameServer['loginServerUp'];
		$cserv->nodeValue  = (int)$gameServer['charServerUp'];
		$mserv->nodeValue  = (int)$gameServer['mapServerUp'];
		$online->nodeValue = (int)$gameServer['playersOnline'];
		$atmerc->nodeValue = (int)$gameServer['autotradeMerchants'];
		$population->nodeValue = (int)$gameServer['population'];
		
		$serv->appendChild($lserv);
		$serv->appendChild($cserv);
		$serv->appendChild($mserv);
		$serv->appendChild($online);
		$serv->appendChild($atmerc);
		$serv->appendChild($population);
		
		// Highest Players Online value recorded, with the time it was reached (ISO 8601).
		if (isset($serverPeak[$privServerName][$serverName])) {
			$peak = $serverPeak[$privServerName][$serverName];
			$serv->setAttribute('peakPlayersOnline', (int)$peak['peak']);
			$serv->setAttribute('peakReachedAt', $peak['time'] ? date('c', (int)$peak['time']) : '');
		}
		
		// War of Emperium state: in progress now, or the next scheduled window.
		if (isset($woeStatus[$privServerName][$serverName]) && $woeStatus[$privServerName][$serverName]['configured']) {
			$woe     = $woeStatus[$privServerName][$serverName];
			$woeElem = $dom->createElement('WoE');
			$woeElem->setAttribute('active', (int)$woe['active']);
			$woeElem->setAttribute('start', $woe['start']);
			$woeElem->setAttribute('end', $woe['end']);
			if (!$woe['active'] && $woe['in']) {
				$woeElem->setAttribute('startsIn', $woe['in']);
			}
			foreach ($woe['castles'] as $castleName) {
				$castleElem = $dom->createElement('Castle');
				$castleElem->setAttribute('name', $castleName);
				$woeElem->appendChild($castleElem);
			}
			$serv->appendChild($woeElem);
		}
		
		$group->appendChild($serv);
	}
	
	$root->appendChild($group);
}

$dom->appendChild($root);

header('Content-Type: text/xml');
echo $dom->saveXML();
exit;
?>