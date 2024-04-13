<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class AxisDeck extends APP_GameClass
{
	const DECK = [
		1 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
			FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [LWOW, ROMANIA, BESSARABIA, KIEV, MOGILEV, BREST, WARSAW, HUNGARY]],
				['name' => 'move/attack', 'containing' => true]
			]
		],
		2 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
			FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [BREST, LWOW, MOGILEV, SMOLENSK, BALTICSTATES, EASTPRUSSIA, WARSAW]],
				['name' => 'move/attack', 'containing' => true]
			]
		],
		3 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
			FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [EASTPRUSSIA, WARSAW, BREST, BALTICSTATES, BALTICSEA, WESTBALTICSEA]],
				['name' => 'move/attack', 'containing' => true]
			]
		],
		4 => ['faction' => Factions::GERMANY, 'reaction' => 'AntiAir',
			FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::GERMANY], 'locations' => Board::W1941],
				['name' => 'eliminate', 'types' => [Pieces::AIRPLANE], 'range' => 2]
			]
		],
		5 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance',
			FIRST_GAME => [
				['name' => 'attack', 'factions' => [Factions::GERMANY], 'locations' => Board::W1939],
				['name' => 'action']
			]
		],
		6 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance',
			FIRST_GAME => [
				['name' => 'draw', 'count' => 2],
				['name' => 'action']
			]
		],
		7 => ['faction' => Factions::PACT, 'reaction' => 'SustainAttack',
			FIRST_GAME => [
				['name' => 'move', 'types' => [Pieces::TANK], 'factions' => [Factions::PACT]],
				['name' => 'attack', 'containing' => true]
			]
		],
		15 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance'],
		16 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance'],
		17 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance'],
		18 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance'],
		19 => ['faction' => Factions::GERMANY, 'reaction' => 'StandFast'],
		20 => ['faction' => Factions::GERMANY, 'reaction' => 'Exchange'],
		21 => ['faction' => Factions::GERMANY, 'reaction' => 'AntiAir'],
		22 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance'],
		23 => ['faction' => Factions::GERMANY, 'reaction' => 'Exchange'],
		24 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack'],
		25 => ['faction' => Factions::PACT, 'reaction' => 'SustainAttack'],
		26 => ['faction' => Factions::PACT, 'reaction' => 'SustainAttack'],
		27 => ['faction' => Factions::PACT, 'reaction' => 'Advance'],
		28 => ['faction' => Factions::PACT, 'reaction' => 'Exchange'],
		29 => ['faction' => Factions::PACT, 'reaction' => 'AntiAir'],
		30 => ['faction' => Factions::PACT, 'reaction' => 'Retreat'],
		31 => ['faction' => Factions::PACT, 'reaction' => 'Retreat'],
		32 => ['faction' => Factions::PACT, 'reaction' => 'Retreat'],
		33 => ['faction' => Factions::PACT, 'reaction' => 'SustainAttack'],
//
		101 => ['faction' => Factions::GERMANY,
			INITIAL_SIDE => [
				['name' => 'deploy', 'types' => [Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::GERMANY], 'locations' => Board::SUPPLY[Factions::GERMANY]],
			],
			SECOND_SIDE => [
			],
		],
		102 => ['faction' => Factions::GERMANY,
			INITIAL_SIDE => [
				['name' => 'attack', 'factions' => [Factions::GERMANY], 'locations' => Board::E1941],
			],
			SECOND_SIDE => [
			],
		],
		103 => ['faction' => Factions::GERMANY,
			INITIAL_SIDE => [
				['name' => 'deploy', 'types' => [Pieces::FLEET], 'factions' => [Factions::GERMANY], 'locations' => [WESTBALTICSEA]],
				['name' => 'discard'],
			],
			SECOND_SIDE => [
			],
		],
		104 => ['faction' => Factions::GERMANY,
			INITIAL_SIDE => [
				['name' => 'VP'],
				['name' => 'draw', 'count' => 3],
			],
			SECOND_SIDE => [
			],
		],
		105 => ['faction' => Factions::GERMANY,
			INITIAL_SIDE => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT], 'locations' => Board::SUPPLY[Factions::PACT]],
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT], 'locations' => Board::SUPPLY[Factions::PACT]],
			],
			SECOND_SIDE => [
			],
		],
	];
//
	static function init($deck)
	{
		$deck->init("axisDeck");
		return $deck;
	}
	static function setup($deck)
	{
//
// ➤ 49 Axis cards (31 Germany, 18 Pact)
//
		$DECK = [];
		for ($index = 1; $index <= 7; $index++) $DECK[] = ['type' => FIRST_GAME, 'type_arg' => $index, 'nbr' => 1];
		for ($index = 15; $index <= 33; $index++) $DECK[] = ['type' => MID, 'type_arg' => $index, 'nbr' => 1];
		$deck->createCards($DECK, 'deck');
//
		$ASIDE = [];
		for ($index = 49; $index <= 71; $index++) $ASIDE[] = ['type' => LATE, 'type_arg' => $index, 'nbr' => 1];
		$deck->createCards($ASIDE, 'aside');
//
// ➤ 5 Axis Contingency cards (3 Germany, 2 Pact)
//
		$CONTINGENCY = [];
		for ($index = 101; $index <= 105; $index++) $CONTINGENCY[] = ['type' => INITIAL_SIDE, 'type_arg' => $index, 'nbr' => 1];
		$deck->createCards($CONTINGENCY, 'contingency');
//
	}
	static function standFast(int $card, string $location, array $pieces)
	{
		switch ($card)
		{
			case 19: // Defending infantry
				$infantry = false;
				foreach ($pieces as $piece) if (Pieces::get($piece)['type'] === 'infantry') $infantry = true;
				return $infantry;
			default:
				return false;
		}
	}
}
