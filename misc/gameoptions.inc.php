<?php
//
require_once('modules/PHP/constants.inc.php');
//
$game_options = [
	DUEL => ['name' => totranslate('⚔ DUEL mode ⚔'), 'values' => [
			0 => ['name' => totranslate('Off')],
			1 => ['name' => totranslate('On'), 'description' => totranslate('Players play the game twice, swapping factions'), 'tmdisplay' => totranslate('⚔ DUEL mode ON ⚔')],
		],
		'displaycondition' => [
			['type' => 'maxplayers', 'value' => 2],
		],
		'notdisplayedmessage' => totranslate("Duel mode option is only available for a two players game"),
	],
	FACTIONSCHOICE => ['name' => totranslate('Factions'), 'values' => [
			0 => ['name' => totranslate('Faction is randomly chosen')],
			1 => ['name' => totranslate('Table administrator will play Allies'), 'description' => totranslate('Admin will play the Soviet Union')],
			2 => ['name' => totranslate('Table administrator will play Axis'), 'description' => totranslate('Admin will play Germany and the Pact')],
		],
		'displaycondition' => [
			['type' => 'maxplayers', 'value' => 2],
		],
	],
	FIRSTGAME => ['name' => totranslate('First game'), 'values' => [
			0 => ['name' => totranslate('Off')],
			1 => ['name' => totranslate('On'), 'description' => totranslate('Players with cards labeled "First Game" in hand')],
		],
	],
];
//
$game_preferences = [
	SPEED => ['name' => totranslate('Animation speed'), 'needReload' => false, 'default' => NORMAL,
		'values' => [
			SLOW => ['name' => totranslate('Slow')],
			NORMAL => ['name' => totranslate('Normal')],
			FAST => ['name' => totranslate('Fast')],
		]],
];
