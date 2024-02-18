<?php
$machinestates = [
	1 => [
		'name' => 'gameSetup',
		'description' => '',
		'type' => 'manager',
		'action' => 'stGameSetup',
		'transitions' => ['' => 2]
	],
	2 => [
		'name' => 'gameSetup',
		'type' => 'game',
		'action' => 'stGameSetup',
		'transitions' => ['startOfGame' => 10]
	],
	5 => [
		'name' => 'mulligan',
		'description' => clienttranslate('Players can take a mulligan'),
		'descriptionmyturn' => clienttranslate('${you} can take a mulligan'),
		'type' => 'multipleactiveplayer',
		'args' => 'argMulligan',
		'possibleactions' => ['mulligan'],
		'transitions' => ['startOfGame' => 10]
	],
//
// Main loop
//
	10 => [
		'name' => 'startOfRound',
		'type' => 'game',
		'action' => 'stStartOfRound',
		'transitions' => ['startOfFactionRound' => 100]
	],
	20 => [
		'name' => 'endOfRound',
		'type' => 'game',
		'action' => 'stEndOfRound',
		'transitions' => ['startOfRound' => 10, 'endOfGame' => 99]
	],
	99 => [
		'name' => 'gameEnd',
		'description' => clienttranslate('End of game'),
		'type' => 'manager',
		'action' => 'stGameEnd',
		'args' => 'argGameEnd'
	],
//
// Main loop
//
	100 => [
		'name' => 'startOfFactionRound',
		'type' => 'game',
		'action' => 'stStartOfFactionRound',
		'transitions' => ['next' => 110]
	],
	110 => [
		'name' => 'firstMovementStep',
		'description' => clienttranslate('Players may move all of their pieces'),
		'type' => 'game',
		'action' => 'stFirstMovementStep',
		'transitions' => ['next' => 111]
	],
	111 => [
		'name' => 'firstMovementStep',
		'description' => clienttranslate('${actplayer} may move all of their pieces'),
		'descriptionmyturn' => clienttranslate('${you} may move all of your pieces'),
		'type' => 'activeplayer',
		'args' => 'argMovementStep',
		'possibleactions' => ['move', 'pass'],
		'transitions' => ['continue' => 111, 'next' => 120]
	],
	120 => [
		'name' => 'firstActionStep',
		'description' => clienttranslate('Players may take their first action'),
		'type' => 'game',
		'action' => 'stFirstActionStep',
		'transitions' => ['next' => 121]
	],
	121 => [
		'name' => 'firstActionStep',
		'description' => clienttranslate('${actplayer} may take their first action'),
		'descriptionmyturn' => clienttranslate('${you} may take your first action'),
		'type' => 'activeplayer',
		'args' => 'argActionStep',
		'possibleactions' => ['conscription', 'forcedMarch', 'desperateAttack', 'productionInitiative', 'pass'],
		'transitions' => ['action' => 125, 'next' => 130]
	],
	125 => [
		'name' => 'action',
		'description' => clienttranslate('${actplayer} is doing an action'),
		'descriptionmyturn' => clienttranslate('${you} are doing an action'),
		'type' => 'activeplayer',
		'args' => 'argAction',
		'possibleactions' => ['deploy', 'cancel'],
		'transitions' => ['continue' => 125, 'cancel' => 121, 'action' => 121, 'next' => 130]
	],
	130 => [
		'name' => 'secondActionStep',
		'description' => clienttranslate('Players may take their second action'),
		'type' => 'game',
		'action' => 'stSecondActionStep',
		'transitions' => ['next' => 131]
	],
	131 => [
		'name' => 'secondActionStep',
		'description' => clienttranslate('${actplayer} may take their second action'),
		'descriptionmyturn' => clienttranslate('${you} may take your second action'),
		'type' => 'activeplayer',
		'args' => 'argActionStep',
		'possibleactions' => ['conscription', 'forcedMarch', 'desperateAttack', 'productionInitiative', 'pass'],
		'transitions' => ['action' => 135, 'next' => 140]
	],
	135 => [
		'name' => 'action',
		'description' => clienttranslate('${actplayer} is doing an action'),
		'descriptionmyturn' => clienttranslate('${you} are doing an action'),
		'type' => 'activeplayer',
		'args' => 'argAction',
		'possibleactions' => ['deploy', 'cancel'],
		'transitions' => ['continue' => 135, 'cancel' => 131, 'action' => 131, 'next' => 140]
	],
	140 => [
		'name' => 'secondMovementStep',
		'description' => clienttranslate('Players may move their tanks and fleets'),
		'type' => 'game',
		'action' => 'stSecondMovementStep',
		'transitions' => ['next' => 141]
	],
	141 => [
		'name' => 'secondMovementStep',
		'description' => clienttranslate('${actplayer} may move their tanks and fleets'),
		'descriptionmyturn' => clienttranslate('${you} may move your tanks and fleets'),
		'type' => 'activeplayer',
		'args' => 'argMovementStep',
		'possibleactions' => ['move', 'pass'],
		'transitions' => ['continue' => 141, 'next' => 150]
	],
	150 => [
		'name' => 'supplyStep',
		'description' => clienttranslate('Remove your unsupplied pieces from the board. The other player’s pieces are not affected.'),
		'type' => 'game',
		'action' => 'stSupplyStep',
		'transitions' => ['next' => 160]
	],
	160 => [
		'name' => 'drawStep',
		'description' => clienttranslate('Draw until your hand has 5 cards or until you have drawn 3 cards, whichever comes first'),
		'type' => 'game',
		'action' => 'stDrawStep',
		'transitions' => ['next' => 170]
	],
	170 => [
		'name' => 'endOfFactionRound',
		'type' => 'game',
		'action' => 'stEndOfFactionRound',
		'transitions' => ['startOfFactionRound' => 100, 'endOfRound' => 20]
	],
//
// Action
//
];
