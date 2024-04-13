<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class AlliesDeck extends APP_GameClass
{
	const DECK = [
		8 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance',
			FIRST_GAME => [
				['name' => 'draw', 'count' => 1],
				['name' => 'move', 'types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE, Pieces::FLEET], 'factions' => [Factions::SOVIETUNION]],
				['name' => 'action']
			]
		],
		9 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'AntiAir',
			FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => [MOSCOW]],
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [KIEV, SEVASTOPOL, CAUCASUS, STALINGRAD, SMOLENSK, VORONEZH, MOSCOW, LENINGRAD, VOLOGDA]],
			]
		],
		10 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack',
			FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::E1939],
				['name' => 'attack', 'containing' => true]
			]
		],
		11 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'StandFast',
			FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => 'contain'],
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => 'contain', 'different' => true],
			]
		],
		12 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'NavalCombat',
			FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::FLEET], 'factions' => [Factions::SOVIETUNION], 'locations' => [SEAOFAZOV]],
			]
		],
		13 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange',
			FIRST_GAME => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::W1939],
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::W1939, 'different' => true],
			]
		],
		14 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange',
			FIRST_GAME => [
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
			INITIAL_SIDE => [
				['name' => 'deploy', 'types' => [Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::ALL],
			],
			SECOND_SIDE => [
			],
		],
		107 => ['faction' => Factions::GERMANY,
			INITIAL_SIDE => [
				['name' => 'attack', 'factions' => [Factions::SOVIETUNION], 'locations' => Board::ALL],
			],
			SECOND_SIDE => [
			],
		],
		108 => ['faction' => Factions::GERMANY,
			INITIAL_SIDE => [
				['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::SUPPLY[Factions::SOVIETUNION]],
				['name' => 'attack', 'containing' => true]
			],
			SECOND_SIDE => [
			],
		],
		109 => ['faction' => Factions::GERMANY,
			INITIAL_SIDE => [
				['name' => 'VP'],
				['name' => 'draw', 'count' => 3],
			],
			SECOND_SIDE => [
			],
		],
		110 => ['faction' => Factions::GERMANY,
			INITIAL_SIDE => [
				['name' => 'deploy', 'types' => [Pieces::FLEET], 'factions' => [Factions::SOVIETUNION], 'locations' => [ADRIATICSEA, DNIEPRRIVER, VOLGARIVER, LAKEPEIPUS, RYBINSKSEA, LAKELADOGA, LAKEONEGA]],
			],
			SECOND_SIDE => [
			],
		],
	];
//
	static function init($deck)
	{
		$deck->init("alliesDeck");
		return $deck;
	}
	static function setup($deck)
	{
//
// ➤ 51 Soviet Union cards
//
		for ($index = 8; $index <= 14; $index++) $DECK[] = ['type' => FIRST_GAME, 'type_arg' => $index, 'nbr' => 1];
		for ($index = 34; $index <= 48; $index++) $DECK[] = ['type' => MID, 'type_arg' => $index, 'nbr' => 1];
		$deck->createCards($DECK, 'deck');
//
		$ASIDE = [];
		for ($index = 72; $index <= 100; $index++) $ASIDE[] = ['type' => LATE, 'type_arg' => $index, 'nbr' => 1];
		$deck->createCards($ASIDE, 'aside');
//
// ➤ 5 Soviet Union Contingency cards
//
		$CONTINGENCY = [];
		for ($index = 106; $index <= 110; $index++) $CONTINGENCY[] = ['type' => INITIAL_SIDE, 'type_arg' => $index, 'nbr' => 1];
		$deck->createCards($CONTINGENCY, 'contingency');
//
	}
	static function standFast(int $card, string $location, array $pieces)
	{
		switch ($card)
		{
			case 11: // Defending infantry
				$infantry = false;
				foreach ($pieces as $piece) if (Pieces::get($piece)['type'] === 'infantry') $infantry = true;
				return $infantry;
			case 35:
				return $location === LENINGRAD;
			case 42:
				return $location === SEVASTOPOL;
			default:
				return false;
		}
	}
}
