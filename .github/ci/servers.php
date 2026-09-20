<?php
// CI-only override of config/servers.php, used by the W3C validation
// workflow. Points DbConfig at the mariadb service container; the game
// server addresses are left unreachable on purpose since no Hercules
// server is booted for this job; only DB-backed pages are validated.
return array(
	array(
		'ServerName'     => 'FluxRO',
		'DbConfig'       => array(
			'Encoding'   => 'utf8',
			'Convert'    => 'utf8',
			'Hostname'   => 'db.ci',
			'Port'       => 3306,
			'Username'   => 'ragnarok',
			'Password'   => 'ragnarok',
			'Database'   => 'ragnarok',
			'Persistent' => false,
			'Timezone'   => null,
		),
		'LogsDbConfig'   => array(
			'Encoding'   => 'utf8',
			'Convert'    => 'utf8',
			'Hostname'   => 'db.ci',
			'Port'       => 3306,
			'Username'   => 'ragnarok',
			'Password'   => 'ragnarok',
			'Database'   => 'ragnarok',
			'Persistent' => false,
			'Timezone'   => null,
		),
		'LoginServer'    => array(
			'Address'  => '127.0.0.1',
			'Port'     => 6900,
			'UseMD5'   => true,
			'NoCase'   => true,
			'GroupID'  => 0,
		),
		'CharMapServers' => array(
			array(
				'ServerName'   => 'FluxRO',
				'Renewal'      => true,
				'MaxCharSlots' => 9,
				'DateTimezone' => null,
				'MaxBaseLevel' => 150,
				'ExpRates' => array(
					'Base' => 100,
					'Job'  => 100,
					'Mvp'  => 100,
				),
				'DropRates' => array(
					'Common'      => 100,
					'CommonBoss'  => 100,
					'Heal'        => 100,
					'HealBoss'    => 100,
					'Useable'     => 100,
					'UseableBoss' => 100,
					'Equip'       => 100,
					'EquipBoss'   => 100,
					'Card'        => 100,
					'CardBoss'    => 100,
					'MvpItem'     => 100,
				),
				'CharServer' => array(
					'Address' => '127.0.0.1',
					'Port'    => 6121,
				),
				'MapServer' => array(
					'Address' => '127.0.0.1',
					'Port'    => 5121,
				),
				'WoeDayTimes' => array(),
				'WoeDisallow' => array(
					array('module' => 'character', 'action' => 'online'),
					array('module' => 'character', 'action' => 'mapstats'),
				),
			),
		),
	),
);
