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
		'transitions' => ['firstMovementStep' => 115]
	],
	115 => [
		'name' => 'firstMovementStep',
		'description' => clienttranslate('${actplayer} may move all of their pieces'),
		'descriptionmyturn' => clienttranslate('${you} may move all of your pieces'),
		'type' => 'activeplayer',
		'args' => 'argMovementStep',
		'possibleactions' => ['move', 'pass'],
		'transitions' => ['continue' => 115, 'next' => 120]
	],
	120 => [
		'name' => 'actionStep',
		'description' => clienttranslate('Players may take their action'),
		'type' => 'game',
		'action' => 'stActionStep',
		'transitions' => ['actionStep' => 125, 'next' => 140]
	],
	125 => [
		'name' => 'actionStep',
		'description' => clienttranslate('${actplayer} may take their ${action} action'),
		'descriptionmyturn' => clienttranslate('${you} may take your ${action} action'),
		'type' => 'activeplayer',
		'args' => 'argActionStep',
		'possibleactions' => ['conscription', 'forcedMarch', 'desperateAttack', 'productionInitiative', 'pass'],
		'transitions' => ['action' => 130, 'next' => 120]
	],
	130 => [
		'name' => 'action',
		'description' => clienttranslate('${actplayer} is doing an action'),
		'descriptionmyturn' => clienttranslate('${you} are doing an action'),
		'type' => 'activeplayer',
		'args' => 'argAction',
		'possibleactions' => ['deploy', 'move', 'attack', 'cancel'],
		'transitions' => ['continue' => 130, 'cancel' => 125, 'action' => 125, 'attack' => 200, 'next' => 120]
	],
	140 => [
		'name' => 'secondMovementStep',
		'description' => clienttranslate('Players may move their tanks and fleets'),
		'type' => 'game',
		'action' => 'stSecondMovementStep',
		'transitions' => ['next' => 145]
	],
	145 => [
		'name' => 'secondMovementStep',
		'description' => clienttranslate('${actplayer} may move their tanks and fleets'),
		'descriptionmyturn' => clienttranslate('${you} may move your tanks and fleets'),
		'type' => 'activeplayer',
		'args' => 'argMovementStep',
		'possibleactions' => ['move', 'pass'],
		'transitions' => ['continue' => 145, 'next' => 150]
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
	200 => [
		'name' => 'attackRound',
		'description' => clienttranslate('Attack round'),
		'type' => 'game',
		'action' => 'stAttackRound',
		'transitions' => ['attackRoundDefender' => 210]
	],
	210 => [
		'name' => 'attackRoundDefender',
		'description' => clienttranslate('Defender attack round'),
		'type' => 'game',
		'action' => 'stAttackRoundDefender',
		'transitions' => ['continue' => 215, 'end' => 125]
	],
	215 => [
		'name' => 'attackRoundDefender',
		'description' => clienttranslate('${actplayer} must remove a piece'),
		'descriptionmyturn' => clienttranslate('${you} must remove a piece'),
		'type' => 'activeplayer',
		'args' => 'argAttackRoundDefender',
		'possibleactions' => ['reaction', 'removePiece'],
		'transitions' => ['reaction' => 215, 'continue' => 220]
	],
	220 => [
		'name' => 'attackRoundAttacker',
		'description' => clienttranslate('Attacker attack round'),
		'type' => 'game',
		'action' => 'stAttackRoundAttacker',
		'transitions' => ['continue' => 225, 'end' => 125]
	],
	225 => [
		'name' => 'attackRoundAttacker',
		'description' => clienttranslate('${actplayer} can remove a piece to initiate a new combat round'),
		'descriptionmyturn' => clienttranslate('${you} can remove a piece to initiate a new combat round'),
		'type' => 'activeplayer',
		'args' => 'argAttackRoundAttacker',
		'possibleactions' => ['reaction', 'removePiece'],
		'transitions' => ['reaction' => 225, 'continue' => 210]
	],
];
