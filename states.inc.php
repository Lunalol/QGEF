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
		'transitions' => ['mulligan' => 5, 'startOfGame' => 10]
	],
	5 => [
		'name' => 'mulligan',
		'description' => clienttranslate('Players can take a mulligan'),
		'descriptionmyturn' => clienttranslate('${you} can take a mulligan'),
		'type' => 'multipleactiveplayer',
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
		'transitions' => ['startOfRound' => 10, 'endOfGame' => 99],
		'updateGameProgression' => true
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
		'name' => '_firstMovementStep',
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
		'possibleactions' => ['cancel', 'remove', 'move', 'pass'],
		'transitions' => ['cancel' => 115, 'continue' => 115, 'next' => 120]
	],
	120 => [
		'name' => '_actionStep',
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
		'possibleactions' => ['play', 'conscription', 'forcedMarch', 'desperateAttack', 'productionInitiative', 'pass'],
		'transitions' => ['action' => 130, 'next' => 120]
	],
	130 => [
		'name' => '_action',
		'description' => clienttranslate('A player is doing an action'),
		'type' => 'game',
		'action' => 'stAction',
		'transitions' => ['continue' => 135, 'interrupt' => 300, 'action' => 130, 'next' => 120]
	],
	135 => [
		'name' => 'action',
		'description' => clienttranslate('${actplayer} is doing an action'),
		'descriptionmyturn' => clienttranslate('${you} are doing an action'),
		'type' => 'activeplayer',
		'args' => 'argAction',
		'possibleactions' => ['remove', 'scorched', 'deploy', 'recruit', 'move', 'attack', 'removePiece', 'discard', 'pass', 'cancel'],
		'transitions' => ['continue' => 135, 'action' => 130, 'cancel' => 130, 'attack' => 200, 'next' => 120]
	],
	140 => [
		'name' => '_secondMovementStep',
		'description' => clienttranslate('Players may move their tanks and fleets'),
		'type' => 'game',
		'action' => 'stSecondMovementStep',
		'transitions' => ['next' => 145, 'skip' => 150]
	],
	145 => [
		'name' => 'secondMovementStep',
		'description' => clienttranslate('${actplayer} may move their tanks and fleets'),
		'descriptionmyturn' => clienttranslate('${you} may move your tanks and fleets'),
		'type' => 'activeplayer',
		'args' => 'argMovementStep',
		'possibleactions' => ['cancel', 'remove', 'move', 'pass'],
		'transitions' => ['cancel' => 145, 'continue' => 145, 'next' => 150]
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
// Combat
//
	200 => [
		'name' => 'attackRound',
		'type' => 'game',
		'action' => 'stAttackRound',
		'transitions' => ['attackRoundDefender' => 210]
	],
	210 => [
		'name' => '_attackRoundDefender',
		'type' => 'game',
		'action' => 'stAttackRoundDefender',
		'transitions' => ['continue' => 215, 'advance' => 250]
	],
	215 => [
		'name' => 'attackRoundDefender',
		'description' => clienttranslate('${actplayer} must remove a piece'),
		'descriptionmyturn' => clienttranslate('${you} must remove a piece'),
		'type' => 'activeplayer',
		'args' => 'argAttackRoundDefender',
		'possibleactions' => ['reaction', 'removePiece'],
		'transitions' => ['reaction' => 215, 'retreat' => 210, 'exchange' => 230, 'continue' => 220]
	],
	220 => [
		'name' => '_attackRoundAttacker',
		'type' => 'game',
		'action' => 'stAttackRoundAttacker',
		'transitions' => ['continue' => 225, 'advance' => 250, 'endCombat' => 260]
	],
	225 => [
		'name' => 'attackRoundAttacker',
		'description' => clienttranslate('${actplayer} can remove a piece to initiate a new combat round'),
		'descriptionmyturn' => clienttranslate('${you} can remove a piece to initiate a new combat round'),
		'type' => 'activeplayer',
		'args' => 'argAttackRoundAttacker',
		'possibleactions' => ['reaction', 'removePiece', 'pass'],
		'transitions' => ['reaction' => 225, 'continue' => 210, 'endCombat' => 260]
	],
	230 => [
		'name' => '_attackRoundExchange',
		'type' => 'game',
		'action' => 'stAttackRoundExchange',
		'transitions' => ['continue' => 235]
	],
	235 => [
		'name' => 'attackRoundExchange',
		'description' => clienttranslate('${actplayer} must remove a piece in exchange'),
		'descriptionmyturn' => clienttranslate('${you} must remove a piece in exchange'),
		'type' => 'activeplayer',
		'args' => 'argAttackRoundExchange',
		'possibleactions' => ['removePiece'],
		'transitions' => ['special' => 240, 'continue' => 220]
	],
	240 => [
		'name' => '_attackRoundSpecial',
		'type' => 'game',
		'action' => 'stAttackRoundSpecial',
		'transitions' => ['continue' => 245]
	],
	245 => [
		'name' => 'attackRoundSpecial',
		'description' => clienttranslate('${actplayer} can play an additional reaction'),
		'descriptionmyturn' => clienttranslate('${you} can play an additional reaction'),
		'type' => 'activeplayer',
		'args' => 'argAttackRoundSpecial',
		'possibleactions' => ['reaction', 'pass'],
		'transitions' => ['continue' => 230, 'pass' => 220]
	],
	250 => [
		'name' => 'attackRoundAdvance',
		'description' => clienttranslate('${actplayer} can advance after combat'),
		'descriptionmyturn' => clienttranslate('${you} can advance after combat'),
		'type' => 'activeplayer',
		'args' => 'argAttackRoundAdvance',
		'possibleactions' => ['reaction', 'pass'],
		'transitions' => ['endCombat' => 260]
	],
	260 => [
		'name' => 'endCosmbat',
		'type' => 'game',
		'action' => 'action',
		'transitions' => ['continue' => 135, 'action' => 130, 'next' => 120]
	],
//
// Other player interuption
//
	300 => [
		'name' => 'interrupt',
		'type' => 'game',
		'action' => 'stInterrupt',
		'transitions' => ['continue' => 310, 'action' => 130, 'next' => 120]
	],
	310 => [
		'name' => 'action',
		'description' => clienttranslate('${actplayer} is doing an action'),
		'descriptionmyturn' => clienttranslate('${you} are doing an action'),
		'type' => 'activeplayer',
		'args' => 'argAction',
		'possibleactions' => ['removePiece', 'discard', 'VP'],
		'transitions' => ['action' => 130, 'next' => 120]
	],
];
