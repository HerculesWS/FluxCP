<?php
if (!defined('FLUX_ROOT')) exit;

$title     = Flux::message('WoeTitle');
$dayNames  = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
$woeTimes  = array();
$castleNames = Flux::castleNames();

foreach ($session->loginAthenaGroup->athenaServers as $athenaServer) {
	$times = $athenaServer->woeDayTimes;
	if ($times) {
		$woeTimes[$athenaServer->serverName] = array();
		foreach ($times as $time) {
			$woeTimes[$athenaServer->serverName][] = array(
				'startingDay'  => $dayNames[$time['startingDay']],
				'startingHour' => $time['startingTime'],
				'endingDay'    => $dayNames[$time['endingDay']],
				'endingHour'   => $time['endingTime'],
				'castles'      => array_values(array_filter(array_map(function ($id) use ($castleNames) {
					return isset($castleNames[$id]) ? $castleNames[$id] : null;
				}, isset($time['castles']) ? $time['castles'] : array())))
			);
		}
	}
}
?>