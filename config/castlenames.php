<?php
// Castles grouped by region: region => array(castle ID => names).
// Names are array('iRO' => ..., 'kRO' => ...); the CastleNaming option in application.php picks which one is shown.
// A plain string is also accepted and used for every naming.
// The Castles page lists the regions in this order. Commenting out a castle's entry will remove it
// from the castles page and exclude it from being a factor in the guild ranking.
// A castle placed directly at the top level (castle ID => name, outside any region) is also accepted;
// those castles are listed together under "Other".
return array(
	'Aldebaran' => array(
		 0 => array('iRO' => 'Neuschwanstein', 'kRO' => 'Noisyubantian'),
		 1 => array('iRO' => 'Hohenschwangau', 'kRO' => 'Hohensyubangawoo'),
		 2 => array('iRO' => 'Nuenberg', 'kRO' => 'Nyirenverk'),
		 3 => array('iRO' => 'Wuerzburg', 'kRO' => 'Byirtsburi'),
		 4 => array('iRO' => 'Rothenburg', 'kRO' => 'Rotenburk')
	),
	'Geffen' => array(
		 5 => array('iRO' => 'Repherion', 'kRO' => 'Reprion'),
		 6 => array('iRO' => 'Eeyolbriggar', 'kRO' => 'Yolbriger'),
		 7 => array('iRO' => 'Yesnelph', 'kRO' => 'Isinlife'),
		 8 => array('iRO' => 'Bergel', 'kRO' => 'Berigel'),
		 9 => array('iRO' => 'Mersetzdeitz', 'kRO' => 'Melsedetsu')
	),
	'Payon' => array(
		10 => array('iRO' => 'Bright Arbor', 'kRO' => 'Mingting'),
		11 => array('iRO' => 'Scarlet Palace', 'kRO' => 'Tiantan'),
		12 => array('iRO' => 'Holy Shadow', 'kRO' => 'Fuying'),
		13 => array('iRO' => 'Sacred Altar', 'kRO' => 'Honglou'),
		14 => array('iRO' => 'Bamboo Grove Hill', 'kRO' => 'Zhulinxian')
	),
	'Prontera' => array(
		15 => array('iRO' => 'Kriemhild', 'kRO' => 'Creamhilt'),
		16 => array('iRO' => 'Swanhild', 'kRO' => 'Sbanhealt'),
		17 => array('iRO' => 'Fadhgridh', 'kRO' => 'Lazrigees'),
		18 => array('iRO' => 'Skoegul', 'kRO' => 'Squagul'),
		19 => array('iRO' => 'Gondul', 'kRO' => 'Guindull')
	),
	'Novice' => array(
		20 => array('iRO' => 'Novice Aldebaran', 'kRO' => 'Novice Aldebaran'),
		21 => array('iRO' => 'Novice Geffen', 'kRO' => 'Novice Geffen'),
		22 => array('iRO' => 'Novice Payon', 'kRO' => 'Novice Payon'),
		23 => array('iRO' => 'Novice Prontera', 'kRO' => 'Novice Prontera')
	),
	'Schwarzwald' => array(
		24 => array('iRO' => 'Himinn', 'kRO' => 'Himinn'),
		25 => array('iRO' => 'Andlangr', 'kRO' => 'Andlangr'),
		26 => array('iRO' => 'Viblainn', 'kRO' => 'Viblainn'),
		27 => array('iRO' => 'Hljod', 'kRO' => 'Hljod'),
		28 => array('iRO' => 'Skidbladnir', 'kRO' => 'Skidbladnir')
	),
	'Arunafeltz' => array(
		29 => array('iRO' => 'Mardol', 'kRO' => 'Mardol'),
		30 => array('iRO' => 'Cyr', 'kRO' => 'Cyr'),
		31 => array('iRO' => 'Horn', 'kRO' => 'Horn'),
		32 => array('iRO' => 'Gefn', 'kRO' => 'Gefn'),
		33 => array('iRO' => 'Bandis', 'kRO' => 'Bandis')
	)
);
