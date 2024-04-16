<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class Decks
{
	const FIRST_GAME = 0;
	const MID = 1;
	const LATE = 2;
	const INITIAL_SIDE = 3; # contingency
	const SECOND_SIDE = 4;  # contingency
//
	const alliesDECK = [
		8 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance',
			self::FIRST_GAME => [
				['name' => 'draw', 'count' => 1],
				['name' => 'move', 'types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE, Pieces::FLEET], 'factions' => [Factions::SOVIETUNION]],
				['name' => 'action']
			]
		],
		9 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'AntiAir',
			self::FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => [MOSCOW]],
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [KIEV, SEVASTOPOL, CAUCASUS, STALINGRAD, SMOLENSK, VORONEZH, MOSCOW, LENINGRAD, VOLOGDA]],
			]
		],
		10 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack',
			self::FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::E1939],
				['name' => 'attack', 'containing' => true]
			]
		],
		11 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'StandFast',
			self::FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => 'contain'],
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => 'contain', 'different' => true],
			]
		],
		12 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'NavalCombat',
			self::FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::FLEET], 'factions' => [Factions::SOVIETUNION], 'locations' => [SEAOFAZOV]],
			]
		],
		13 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange',
			self::FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::W1939],
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::W1939, 'different' => true],
			]
		],
		14 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange',
			self::FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::E1939],
				['name' => 'action']
			]
		],
		34 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Retreat'],
		35 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'StandFast'],
		36 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'NavalCombat'],
		37 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange'],
		38 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange'],
		39 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance'],
		40 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Retreat'],
		41 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack'],
		42 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'StandFast'],
		43 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange'],
		44 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack'],
		45 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance'],
		46 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange'],
		47 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange'],
		48 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance'],
//
		106 => ['faction' => Factions::GERMANY,
			self::INITIAL_SIDE => [
				['name' => 'deploy', 'types' => [Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::ALL],
			],
			self::SECOND_SIDE => [
			],
		],
		107 => ['faction' => Factions::GERMANY,
			self::INITIAL_SIDE => [
				['name' => 'attack', 'factions' => [Factions::SOVIETUNION], 'locations' => Board::ALL],
			],
			self::SECOND_SIDE => [
			],
		],
		108 => ['faction' => Factions::GERMANY,
			self::INITIAL_SIDE => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::SUPPLY[Factions::SOVIETUNION]],
				['name' => 'attack', 'containing' => true]
			],
			self::SECOND_SIDE => [
			],
		],
		109 => ['faction' => Factions::GERMANY,
			self::INITIAL_SIDE => [
				['name' => 'VP'],
				['name' => 'draw', 'count' => 3],
			],
			self::SECOND_SIDE => [
			],
		],
		110 => ['faction' => Factions::GERMANY,
			self::INITIAL_SIDE => [
				['name' => 'deploy', 'types' => [Pieces::FLEET], 'factions' => [Factions::SOVIETUNION], 'locations' => [ADRIATICSEA, DNIEPRRIVER, VOLGARIVER, LAKEPEIPUS, RYBINSKSEA, LAKELADOGA, LAKEONEGA]],
			],
			self::SECOND_SIDE => [
			],
		],
	];
//
	const axisDECK = [
		1 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
			self::FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [LWOW, ROMANIA, BESSARABIA, KIEV, MOGILEV, BREST, WARSAW, HUNGARY]],
				['name' => 'move/attack', 'containing' => true]
			]
		],
		2 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
			self::FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [BREST, LWOW, MOGILEV, SMOLENSK, BALTICSTATES, EASTPRUSSIA, WARSAW]],
				['name' => 'move/attack', 'containing' => true]
			]
		],
		3 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
			self::FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [EASTPRUSSIA, WARSAW, BREST, BALTICSTATES, BALTICSEA, WESTBALTICSEA]],
				['name' => 'move/attack', 'containing' => true]
			]
		],
		4 => ['faction' => Factions::GERMANY, 'reaction' => 'AntiAir',
			self::FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::GERMANY], 'locations' => Board::W1941],
				['name' => 'eliminate', 'types' => [Pieces::AIRPLANE], 'range' => 2]
			]
		],
		5 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance',
			self::FIRST_GAME => [
				['name' => 'attack', 'factions' => [Factions::GERMANY], 'locations' => Board::W1939],
				['name' => 'action']
			]
		],
		6 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance',
			self::FIRST_GAME => [
				['name' => 'draw', 'count' => 2],
				['name' => 'action']
			]
		],
		7 => ['faction' => Factions::PACT, 'reaction' => 'SustainAttack',
			self::FIRST_GAME => [
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
			self::INITIAL_SIDE => [
				['name' => 'deploy', 'types' => [Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::GERMANY], 'locations' => Board::SUPPLY[Factions::GERMANY]],
			],
			self::SECOND_SIDE => [
			],
		],
		102 => ['faction' => Factions::GERMANY,
			self::INITIAL_SIDE => [
				['name' => 'attack', 'factions' => [Factions::GERMANY], 'locations' => Board::E1941],
			],
			self::SECOND_SIDE => [
			],
		],
		103 => ['faction' => Factions::GERMANY,
			self::INITIAL_SIDE => [
				['name' => 'deploy', 'types' => [Pieces::FLEET], 'factions' => [Factions::GERMANY], 'locations' => [WESTBALTICSEA]],
				['name' => 'discard'],
			],
			self::SECOND_SIDE => [
			],
		],
		104 => ['faction' => Factions::GERMANY,
			self::INITIAL_SIDE => [
				['name' => 'VP'],
				['name' => 'draw', 'count' => 3],
			],
			self::SECOND_SIDE => [
			],
		],
		105 => ['faction' => Factions::GERMANY,
			self::INITIAL_SIDE => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT], 'locations' => Board::SUPPLY[Factions::PACT]],
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT], 'locations' => Board::SUPPLY[Factions::PACT]],
			],
			self::SECOND_SIDE => [
			],
		],
	];
//
	static function setupAllies($decks)
	{
//
// ➤ 51 Soviet Union cards
//
		for ($index = 8; $index <= 14; $index++) $alliesDECK[] = ['type' => self::FIRST_GAME, 'type_arg' => $index, 'nbr' => 1];
		for ($index = 34; $index <= 48; $index++) $alliesDECK[] = ['type' => self::MID, 'type_arg' => $index, 'nbr' => 1];
		$decks->createCards($alliesDECK, Factions::ALLIES);
//
		$ASIDE = [];
		for ($index = 72; $index <= 100; $index++) $ASIDE[] = ['type' => self::LATE, 'type_arg' => $index, 'nbr' => 1];
		$decks->createCards($ASIDE, 'aside', Factions::ALLIES);
//
// ➤ 5 Soviet Union Contingency cards
//
		$CONTINGENCY = [];
		for ($index = 106; $index <= 110; $index++) $CONTINGENCY[] = ['type' => self::INITIAL_SIDE, 'type_arg' => $index, 'nbr' => 1];
		$decks->createCards($CONTINGENCY, 'contingency', Factions::ALLIES);
	}
	static function setupAxis($decks)
	{
//
// ➤ 49 Axis cards (31 Germany, 18 Pact)
//
		$axisDECK = [];
		for ($index = 1; $index <= 7; $index++) $axisDECK[] = ['type' => self::FIRST_GAME, 'type_arg' => $index, 'nbr' => 1];
		for ($index = 15; $index <= 33; $index++) $axisDECK[] = ['type' => self::MID, 'type_arg' => $index, 'nbr' => 1];
		$decks->createCards($axisDECK, Factions::AXIS);
//
		$ASIDE = [];
		for ($index = 49; $index <= 71; $index++) $ASIDE[] = ['type' => self::LATE, 'type_arg' => $index, 'nbr' => 1];
		$decks->createCards($ASIDE, 'aside', Factions::AXIS);
//
// ➤ 5 Axis Contingency cards (3 Germany, 2 Pact)
//
		$CONTINGENCY = [];
		for ($index = 101; $index <= 105; $index++) $CONTINGENCY[] = ['type' => self::INITIAL_SIDE, 'type_arg' => $index, 'nbr' => 1];
		$decks->createCards($CONTINGENCY, 'contingency', Factions::AXIS,);
//
	}
	static function standFast(int $card, string $location, array $pieces)
	{
		switch ($card)
		{
			case 11: // Defending infantry
				foreach ($pieces as $piece) if (Pieces::get($piece)['type'] === 'infantry') return true;
				return false;
			case 19: // Defending infantry
				foreach ($pieces as $piece) if (Pieces::get($piece)['type'] === 'infantry') return true;
				return false;
			case 35:
				return $location === LENINGRAD;
			case 42:
				return $location === SEVASTOPOL;
			default:
				return false;
		}
	}
}
