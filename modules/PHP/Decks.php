<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class Decks extends APP_GameClass
{
	const FIRST_GAME = 0;
	const MID = 1;
	const LATE = 2;
	const INITIAL_SIDE = 3; # contingency
	const SECOND_SIDE = 4;  # contingency
//
	const DECKS = [
		Factions::AXIS => [
//
// First game (1-7)
//
			1 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
				self::FIRST_GAME => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [LWOW, ROMANIA, BESSARABIA, KIEV, MOGILEV, BREST, WARSAW, HUNGARY], 'mandatory' => true],
					['name' => 'move/attack', 'containing' => true]
				]
			],
			2 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
				self::FIRST_GAME => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [BREST, LWOW, MOGILEV, SMOLENSK, BALTICSTATES, EASTPRUSSIA, WARSAW], 'mandatory' => true],
					['name' => 'move/attack', 'containing' => true]
				]
			],
			3 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
				self::FIRST_GAME => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [EASTPRUSSIA, WARSAW, BREST, BALTICSTATES, BALTICSEA, WESTBALTICSEA], 'mandatory' => true],
					['name' => 'move/attack', 'containing' => true]
				]
			],
			4 => ['faction' => Factions::GERMANY, 'reaction' => 'AntiAir',
				self::FIRST_GAME => [
					['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::GERMANY], 'locations' => Board::W1941, 'mandatory' => true],
					['name' => 'eliminate', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'range' => 2]
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
//
// Mid game (15-33)
//
			15 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance',
				self::MID => [
					['name' => 'attack', 'types' => [Pieces::INFANTRY, Pieces::TANK], 'factions' => [Factions::GERMANY],
						'special' => 15, 'advance' => [Pieces::INFANTRY, Pieces::TANK], 'requirement' => 'noSpringTurn'],
				]
			],
			16 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance',
				self::MID => [
					['name' => 'move', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY]],
					['name' => 'move', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'different' => true],
					['name' => 'move', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'different' => true]
				]
			],
			17 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance', 'requirement' => 'noSpringTurn',
				self::MID => [
					['name' => 'move', 'types' => [Pieces::TANK], 'factions' => [Factions::GERMANY]],
					['name' => 'move', 'types' => [Pieces::TANK], 'factions' => [Factions::GERMANY], 'same' => true],
					['name' => 'action']
				]
			],
			18 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance',
				self::MID => [
					['name' => 'attack', 'factions' => [Factions::GERMANY], 'locations' => Board::E1941],
					['name' => 'action']
				]
			],
			19 => ['faction' => Factions::GERMANY, 'reaction' => 'StandFast',
				self::MID => [
					['name' => 'attack', 'factions' => [Factions::GERMANY], 'locations' => Board::ALL, 'special' => 19],
					['name' => 'action']
				]
			],
			20 => ['faction' => Factions::GERMANY, 'reaction' => 'Exchange',
				self::MID => [
					['name' => 'recruit', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [],
						'adjacent' => ['types' => [Pieces::FLEET], 'factions' => [Factions::GERMANY]]],
					['name' => 'move/attack', 'containing' => true]
				]
			],
			21 => ['faction' => Factions::GERMANY, 'reaction' => 'AntiAir',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::GERMANY], 'locations' => Board::W1941],
					['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::GERMANY], 'locations' => Board::W1941, 'different' => true],
				]
			],
			22 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance', 'requirement' => 'noSpringTurn',
				self::MID => [
					['name' => 'move', 'types' => [Pieces::TANK], 'factions' => [Factions::GERMANY]],
					['name' => 'attack', 'containing' => true]
				]
			],
			23 => ['faction' => Factions::GERMANY, 'reaction' => 'Exchange',
				self::MID => [
					['name' => 'eliminate', 'types' => [Pieces::INFANTRY, Pieces::TANK], 'factions' => [Factions::SOVIETUNION], 'locations' => [],
						'adjacent' => ['types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY]]],
				]
			],
			24 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
				self::MID => [
					['name' => 'eliminate', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [], 'mandatory' => true,
						'adjacent' => ['types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY]]],
					['name' => 'attack', 'factions' => [Factions::GERMANY], 'into' => true]
				]
			],
			25 => ['faction' => Factions::PACT, 'reaction' => 'SustainAttack',
				self::MID => [
					['name' => 'attack', 'factions' => [Factions::PACT], 'locations' => Board::ALL, 'advance' => [Pieces::INFANTRY, Pieces::TANK], 'requirement' => 'noSpringTurn'],
				]
			],
			26 => ['faction' => Factions::PACT, 'reaction' => 'SustainAttack',
				self::MID => [
					['name' => 'attack', 'factions' => [Factions::PACT], 'into' => [KARELIA, PETROZAVODSK], 'advance' => [Pieces::INFANTRY],
						'contain' => ['types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT]]],
				]
			],
			27 => ['faction' => Factions::PACT, 'reaction' => 'Advance', 'requirement' => 'noSpringTurn',
				self::MID => [
					['name' => 'move', 'types' => [Pieces::INFANTRY, Pieces::TANK], 'factions' => [Factions::PACT]],
					['name' => 'attack', 'containing' => true]
				]
			],
			28 => ['faction' => Factions::PACT, 'reaction' => 'Exchange',
				self::MID => [
					['name' => 'eliminate', 'types' => [Pieces::INFANTRY, Pieces::TANK], 'factions' => [Factions::SOVIETUNION], 'locations' => [FINLAND, KARELIA]],
					['name' => 'recruit', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT], 'locations' => [FINLAND]],
				]
			],
			29 => ['faction' => Factions::PACT, 'reaction' => 'AntiAir',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::PACT], 'locations' => Board::E1941],
					['name' => 'action']
				]
			],
			30 => ['faction' => Factions::PACT, 'reaction' => 'Retreat',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT], 'locations' => [ROMANIA, BULGARIA, YUGOSLAVIA, HUNGARY, LWOW, BESSARABIA, BLACKSEA, BOSPORUS]],
					['name' => 'move/attack', 'containing' => true]
				]
			],
			31 => ['faction' => Factions::PACT, 'reaction' => 'Retreat',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT], 'locations' => [ROMANIA, BULGARIA, YUGOSLAVIA, HUNGARY, LWOW, BESSARABIA, BLACKSEA, BOSPORUS]],
					['name' => 'move/attack', 'containing' => true]
				]
			],
			32 => ['faction' => Factions::PACT, 'reaction' => 'Retreat',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT], 'locations' => [HUNGARY, YUGOSLAVIA, ROMANIA, LWOW, WARSAW, BERLIN, VIENNA]],
					['name' => 'move/attack', 'containing' => true]
				]
			],
			33 => ['faction' => Factions::PACT, 'reaction' => 'SustainAttack',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT], 'locations' => Board::E1941],
					['name' => 'action']
				]
			],
//
// Late game (49-71)
//
			49 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::GERMANY], 'locations' => Board::ALL],
					['name' => 'move/attack', 'containing' => true, 'requirement' => 'noSpringTurn']
				]
			],
			50 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::GERMANY], 'locations' => Board::E1939, 'mandatory' => true],
					['name' => 'attack', 'containing' => true]
				]
			],
			51 => ['faction' => Factions::GERMANY, 'reaction' => 'Retreat',
				self::LATE => [
				]
			],
			52 => ['faction' => Factions::GERMANY, 'reaction' => 'NavalCombat',
				self::LATE => [
					['name' => 'eliminate', 'types' => [Pieces::FLEET], 'factions' => [Factions::SOVIETUNION], 'locations' => [],
						'adjacent' => ['types' => [Pieces::AIRPLANE, Pieces::FLEET], 'factions' => [Factions::GERMANY]]],
				]
			],
			53 => ['faction' => Factions::GERMANY, 'reaction' => 'NavalCombat',
				self::LATE => [
				]
			],
			54 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
				self::LATE => [
				]
			],
			55 => ['faction' => Factions::GERMANY, 'reaction' => 'AntiAir',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::GERMANY], 'locations' => Board::ALL],
					['name' => 'action']
				]
			],
			56 => ['faction' => Factions::GERMANY, 'reaction' => 'Retreat',
				self::LATE => [
					['name' => 'eliminate', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => Board::ALL, 'mandatory' => true],
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::GERMANY], 'locations' => Board::ALL, 'same' => true],
					['name' => 'action']
				]
			],
			57 => ['faction' => Factions::GERMANY, 'reaction' => 'StandFast',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [BERLIN]],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [VIENNA]]
				]
			],
			58 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack',
				self::LATE => [
					['name' => 'attack', 'factions' => [Factions::GERMANY], 'locations' => Board::ALL, 'special' => 58],
					['name' => 'action']
				]
			],
			59 => ['faction' => Factions::GERMANY, 'reaction' => 'StandFast',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::GERMANY], 'locations' => [],
						'contain' => ['types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY]]],
					['name' => 'action']
				]
			],
			60 => ['faction' => Factions::GERMANY, 'reaction' => 'Exchange',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [],
						'contain' => ['types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::GERMANY]]],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [], 'different' => true,
						'contain' => ['types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::GERMANY]]],
				]
			],
			61 => ['faction' => Factions::GERMANY, 'reaction' => 'Retreat',
				self::LATE => [
				]
			],
			62 => ['faction' => Factions::GERMANY, 'reaction' => 'Exchange',
				self::LATE => [
				]
			],
			63 => ['faction' => Factions::GERMANY, 'reaction' => 'Retreat',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [BERLIN]],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [VIENNA]],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [EASTPRUSSIA]],
					['name' => 'action']
				]
			],
			64 => ['faction' => Factions::PACT, 'reaction' => 'Exchange', 'requirement' => 64,
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::GERMANY], 'locations' => [BERLIN]],
					['name' => 'VP', 'FACTION' => Factions::AXIS]
				]
			],
			65 => ['faction' => Factions::PACT, 'reaction' => 'Retreat', 'requirement' => 65,
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [BERLIN]],
					['name' => 'VP', 'FACTION' => Factions::AXIS]
				]
			],
			66 => ['faction' => Factions::PACT, 'reaction' => 'Retreat',
				self::LATE => [
				]
			],
			67 => ['faction' => Factions::PACT, 'reaction' => 'Retreat',
				self::LATE => [
				]
			],
			68 => ['faction' => Factions::PACT, 'reaction' => 'StandFast',
				self::LATE => [
				]
			],
			69 => ['faction' => Factions::PACT, 'reaction' => 'Exchange',
				self::LATE => [
					['name' => 'draw', 'count' => 2],
				]
			],
			70 => ['faction' => Factions::PACT, 'reaction' => 'SustainAttack',
				self::LATE => [
				]
			],
			71 => ['faction' => Factions::PACT, 'reaction' => 'NavalCombat',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::FLEET], 'factions' => [Factions::PACT], 'locations' => [ROMANIA, BULGARIA, YUGOSLAVIA, HUNGARY, LWOW, BESSARABIA, BLACKSEA, BOSPORUS]]
				]
			],
//
// Contingency (101-105)
//
			101 => ['faction' => Factions::GERMANY,
				self::INITIAL_SIDE => [
					['name' => 'deploy', 'types' => [Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::GERMANY], 'locations' => Board::SUPPLY[Factions::GERMANY]],
				],
				self::SECOND_SIDE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [BERLIN]],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [BERLIN]],
					['name' => 'VP', 'FACTION' => Factions::ALLIES]
				],
			],
			102 => ['faction' => Factions::GERMANY,
				self::INITIAL_SIDE => [
					['name' => 'attack', 'factions' => [Factions::GERMANY], 'into' => Board::E1941],
				],
				self::SECOND_SIDE => [
					['name' => 'eliminateVS', 'types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE, Pieces::FLEET], 'factions' => [Factions::SOVIETUNION], 'locations' => [BERLIN, VIENNA, HUNGARY, WARSAW, WESTBALTICSEA]],
				],
			],
			103 => ['faction' => Factions::GERMANY,
				self::INITIAL_SIDE => [
					['name' => 'deploy', 'types' => [Pieces::FLEET], 'factions' => [Factions::GERMANY], 'locations' => [WESTBALTICSEA], 'trigger' => ['name' => 'discard', 'count' => 1]]
				],
				self::SECOND_SIDE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY], 'locations' => [BERLIN, WARSAW, EASTPRUSSIA]],
				],
			],
			104 => ['faction' => Factions::GERMANY,
				self::INITIAL_SIDE => [
					['name' => 'draw', 'count' => 3],
					['name' => 'VP', 'FACTION' => Factions::ALLIES]
				],
				self::SECOND_SIDE => [
					['name' => 'move', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY]],
					['name' => 'action'],
					['name' => 'VP', 'FACTION' => Factions::ALLIES]
				],
			],
			105 => ['faction' => Factions::GERMANY,
				self::INITIAL_SIDE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT], 'locations' => Board::SUPPLY[Factions::PACT]],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT], 'locations' => Board::SUPPLY[Factions::PACT], 'same' => true],
				],
				self::SECOND_SIDE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::PACT], 'locations' => Board::SUPPLY[Factions::PACT]],
				],
			]],
//
		Factions::ALLIES => [
//
// First game (8-14)
//
			8 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance',
				self::FIRST_GAME => [
					['name' => 'draw', 'count' => 1],
					['name' => 'move', 'types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE, Pieces::FLEET], 'factions' => [Factions::SOVIETUNION], 'noundo' => true],
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
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::E1939, 'mandatory' => true],
					['name' => 'attack', 'containing' => true]
				]
			],
			11 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'StandFast',
				self::FIRST_GAME => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [],
						'contain' => ['types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION],]],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [], 'different' => true,
						'contain' => ['types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION],]],
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
//
// Mid game (34-48)
//
			34 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Retreat',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [GORKI]],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [GORKI]],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [GORKI]],
				]
			],
			35 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'StandFast',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [LENINGRAD]],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [LENINGRAD]],
				]
			],
			36 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'NavalCombat',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::FLEET], 'factions' => [Factions::SOVIETUNION], 'locations' => [GULFOFFINLAND]],
				]
			],
			37 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange',
				self::MID => [
					['name' => 'scorched', 'locations' => [KIEV, SEVASTOPOL, CAUCASUS, STALINGRAD, SMOLENSK, VORONEZH, MOSCOW, LENINGRAD, VOLOGDA]],
					['name' => 'VP', 'FACTION' => Factions::ALLIES]
				]
			],
			38 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange', 'requirement' => 'winterTurn',
				self::MID => [
					['name' => 'draw', 'count' => 1],
					['name' => 'eliminateVS', 'types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE, Pieces::FLEET], 'factions' => [Factions::GERMANY], 'locations' => Board::E1939],
					['name' => 'eliminateVS', 'types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE, Pieces::FLEET], 'factions' => [Factions::PACT], 'locations' => Board::E1939],
				]
			],
			39 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [CAUCASUS]],
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION], 'locations' => [CAUCASUS]],
					['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => [CAUCASUS]],
				]
			],
			40 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Retreat',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::E1939],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::E1939, 'different' => true],
				]
			],
			41 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [], 'mandatory' => true,
						'contain' => ['types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION]]],
					['name' => 'attack', 'containing' => true]
				]
			],
			42 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'StandFast',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [SEVASTOPOL]],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [SEVASTOPOL]],
				]
			],
			43 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange',
				self::MID => [
					['name' => 'move', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION]],
					['name' => 'attack', 'containing' => true]
				]
			],
			44 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack',
				self::MID => [
					['name' => 'eliminate', 'types' => [Pieces::INFANTRY, Pieces::TANK], 'factions' => [Factions::GERMANY, Factions::PACT], 'locations' => [],
						'adjacent' => ['types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION]]],
				]
			],
			45 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION], 'locations' => [KIEV, SEVASTOPOL, CAUCASUS, STALINGRAD, SMOLENSK, VORONEZH, MOSCOW, LENINGRAD, VOLOGDA]],
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION], 'locations' => [KIEV, SEVASTOPOL, CAUCASUS, STALINGRAD, SMOLENSK, VORONEZH, MOSCOW, LENINGRAD, VOLOGDA], 'different' => true],
				]
			],
			46 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange',
				self::MID => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [KIEV, SEVASTOPOL, CAUCASUS, STALINGRAD, SMOLENSK, VORONEZH, MOSCOW, LENINGRAD, VOLOGDA]],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [KIEV, SEVASTOPOL, CAUCASUS, STALINGRAD, SMOLENSK, VORONEZH, MOSCOW, LENINGRAD, VOLOGDA], 'different' => true],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [KIEV, SEVASTOPOL, CAUCASUS, STALINGRAD, SMOLENSK, VORONEZH, MOSCOW, LENINGRAD, VOLOGDA], 'different' => true],
				]
			],
			47 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange',
				self::MID => [
					['name' => 'mud'],
				]
			],
			48 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance', 'requirement' => 48,
				self::MID => [
					['name' => 'draw', 'count' => 3],
					['name' => 'VP', 'FACTION' => Factions::AXIS]
				]
			],
//
// Late game (72-100)
//
			72 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'AntiAir',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [VOLOGDA]],
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION], 'locations' => [VOLOGDA]],
					['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => [VOLOGDA]],
				]
			],
			73 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Retreat',
				self::LATE => [
				]
			],
			74 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION], 'locations' => [],
						'contain' => ['types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION]]],
					['name' => 'attack', 'containing' => true]
				]
			],
			75 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack',
				self::LATE => [
					['name' => 'eliminate', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::GERMANY, Factions::PACT], 'locations' => [], 'mandatory' => true,
						'adjacent' => ['types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE, Pieces::FLEET], 'factions' => [Factions::SOVIETUNION]]],
					['name' => 'attack', 'factions' => [Factions::SOVIETUNION], 'into' => true]
				]
			],
			76 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance',
				self::LATE => [
				]
			],
			77 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [PETROZAVODSK]],
					['name' => 'deploy', 'types' => [Pieces::FLEET], 'factions' => [Factions::SOVIETUNION], 'locations' => [LAKELADOGA]],
				]
			],
			78 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Retreat',
				self::LATE => [
				]
			],
			79 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'StandFast',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [MOSCOW, KURSK, VORONEZH, GORKI, VOLOGDA, RYBINSKSEA, NOVGOROD, SMOLENSK]],
					['name' => 'attack', 'containing' => true]
				]
			],
			80 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack',
				self::LATE => [
				]
			],
			81 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'StandFast',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION], 'locations' => [STALINGRAD, CAUCASUS, GORKI, VOLGARIVER, ROSTOV]],
					['name' => 'attack', 'containing' => true]
				]
			],
			82 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [],
						'contain' => ['types' => [Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION]]],
					['name' => 'attack', 'containing' => true, 'advance' => [Pieces::INFANTRY]]
				]
			],
			83 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'AntiAir',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::E1939, 'mandatory' => true],
					['name' => 'eliminate', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::GERMANY, Factions::PACT], 'range' => 1]
				]
			],
			84 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance',
				self::LATE => [
					['name' => 'draw', 'count' => 2],
				]
			],
			85 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [],
						'contain' => ['types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION]]],
					['name' => 'attack', 'containing' => true]
				]
			],
			86 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Retreat',
				self::LATE => [
					['name' => 'draw', 'count' => 1],
					['name' => 'gorki'],
				]
			],
			87 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack',
				self::LATE => [
					['name' => 'eliminate', 'types' => [Pieces::TANK], 'factions' => [Factions::GERMANY, Factions::PACT], 'locations' => [], 'mandatory' => true,
						'adjacent' => ['types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION]]],
					['name' => 'attack', 'factions' => [Factions::SOVIETUNION], 'into' => true]
				]
			],
			88 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance',
				self::LATE => [
				]
			],
			89 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'AntiAir',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::E1939, 'mandatory' => true],
					['name' => 'attack', 'containing' => true]
				]
			],
			90 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Retreat',
				self::LATE => [
				]
			],
			91 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Retreat',
				self::LATE => [
				]
			],
			92 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack',
				self::LATE => [
					['name' => 'attack', 'factions' => [Factions::SOVIETUNION], 'locations' => Board::ALL],
					['name' => 'action']
				]
			],
			93 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION], 'locations' => [SMOLENSK, MOGILEV, KURSK, MOSCOW, NOVGOROD, LAKEPEIPUS, BALTICSTATES, BREST], 'mandatory' => true],
					['name' => 'attack', 'containing' => true]
				]
			],
			94 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'AntiAir',
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => [KIEV, SEVASTOPOL, CAUCASUS, STALINGRAD, SMOLENSK, VORONEZH, MOSCOW, LENINGRAD, VOLOGDA]],
					['name' => 'deploy', 'types' => [Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => [KIEV, SEVASTOPOL, CAUCASUS, STALINGRAD, SMOLENSK, VORONEZH, MOSCOW, LENINGRAD, VOLOGDA], 'different' => true],
				]
			],
			95 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack',
				self::LATE => [
				]
			],
			96 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'StandFast', 'requirement' => 96,
				self::LATE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => [KURSK, KHARKOV, VORONEZH, MOSCOW, SMOLENSK, MOGILEV]],
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION], 'locations' => [KURSK, KHARKOV, VORONEZH, MOSCOW, SMOLENSK, MOGILEV], 'same' => true],
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION], 'locations' => [KURSK, KHARKOV, VORONEZH, MOSCOW, SMOLENSK, MOGILEV], 'same' => true],
				]
			],
			97 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance',
				self::LATE => [
					['name' => 'draw', 'count' => 1],
					['name' => 'action']
				]
			],
			98 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance',
				self::LATE => [
					['name' => 'draw', 'count' => 3],
					['name' => 'discard', 'count' => 1],
					['name' => 'deploy', 'types' => [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::E1939],
				]
			],
			99 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance',
				self::LATE => [
					['name' => 'move', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION]],
					['name' => 'move', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'different' => true],
					['name' => 'action']
				]
			],
			100 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange',
				self::LATE => [
					['name' => 'action']
				]
			],
//
// Contingency (106-110)
//
			106 => ['faction' => Factions::GERMANY,
				self::INITIAL_SIDE => [
					['name' => 'deploy', 'types' => [Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => [VOLOGDA, GORKI, STALINGRAD, CAUCASUS, PETROZAVODSK, MOSCOW, VORONEZH, ROSTOV]],
				],
				self::SECOND_SIDE => [
					['name' => 'deploy', 'types' => [Pieces::TANK, Pieces::AIRPLANE], 'factions' => [Factions::SOVIETUNION], 'locations' => [GORKI, STALINGRAD, VOLOGDA, MOSCOW, VORONEZH, ROSTOV]],
					['name' => 'VP', 'FACTION' => Factions::AXIS]
				],
			],
			107 => ['faction' => Factions::GERMANY,
				self::INITIAL_SIDE => [
					['name' => 'attack', 'factions' => [Factions::SOVIETUNION], 'locations' => Board::ALL],
				],
				self::SECOND_SIDE => [
					['name' => 'eliminate', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::ALL, 'mandatory' => true],
					['name' => 'deploy', 'types' => [Pieces::TANK], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::ALL, 'same' => true]
				],
			],
			108 => ['faction' => Factions::GERMANY,
				self::INITIAL_SIDE => [
					['name' => 'deploy', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::SUPPLY[Factions::SOVIETUNION], 'mandatory' => true],
					['name' => 'attack', 'containing' => true]
				],
				self::SECOND_SIDE => [
					['name' => 'eliminate', 'types' => [Pieces::INFANTRY], 'factions' => [Factions::SOVIETUNION], 'special' => 108, 'mandatory' => true],
					['name' => 'attack', 'special' => 108]
				],
			],
			109 => ['faction' => Factions::GERMANY,
				self::INITIAL_SIDE => [
					['name' => 'draw', 'count' => 3],
					['name' => 'VP', 'FACTION' => Factions::AXIS]
				],
				self::SECOND_SIDE => [
					['name' => 'draw', 'count' => 2],
					['name' => 'discard', 'count' => 1],
					['name' => 'action'],
				],
			],
			110 => ['faction' => Factions::GERMANY,
				self::INITIAL_SIDE => [
					['name' => 'deploy', 'types' => [Pieces::FLEET], 'factions' => [Factions::SOVIETUNION], 'locations' => [ADRIATICSEA, DNIEPRRIVER, VOLGARIVER, LAKEPEIPUS, RYBINSKSEA, LAKELADOGA, LAKEONEGA]],
				],
				self::SECOND_SIDE => [
					['name' => 'eliminate', 'types' => [Pieces::FLEET], 'factions' => [Factions::SOVIETUNION], 'locations' => Board::ALL, 'mandatory' => true],
					['name' => 'attack', 'factions' => [Factions::SOVIETUNION], 'locations' => [ROMANIA, BESSARABIA]],
				],
			]],
	];
//
	static $table = null;
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
			case 35: // Leningrad
				return $location === LENINGRAD;
			case 42: // Sevastopol
				return $location === SEVASTOPOL;
			case 57: // In or adjacent to Berlin
				return in_array($location, [BERLIN, VIENNA, HUNGARY, WARSAW, WESTBALTICSEA]);
			case 59: // Defending tank
				foreach ($pieces as $piece) if (Pieces::get($piece)['type'] === 'tank') return true;
				return false;
			case 68: // Finland or Karelia
				return in_array($location, [FINLAND, KARELIA]);
			case 79: // Moscow
				return $location === MOSCOW;
			case 35: // Stalingrad
				return $location === STALINGRAD;
			case 96: // Defending tank
				foreach ($pieces as $piece) if (Pieces::get($piece)['type'] === 'tank') return true;
				return false;
			default:
				return false;
		}
	}
	static function special(array $action)
	{
//
		switch ($action['special'])
		{
			case 15:
//
// Attack with a German force containing both an infantry and a tank
//
				return Pieces::getPossibleAttacks(Factions::AXIS, self::$table->getObjectListFromDB(""
							. "(SELECT * FROM pieces WHERE faction = 'germany' AND type = 'INFANTRY' AND location in (SELECT location FROM pieces WHERE faction = 'germany' AND type = 'TANK'))"
							. "UNION "
							. "(SELECT * FROM pieces WHERE faction = 'germany' AND type = 'TANK' AND location in (SELECT location FROM pieces WHERE faction = 'germany' AND type = 'INFANTRY'))"));
//
			case 19:
//
// Attack with a German force containing an infantry
//
				return Pieces::getPossibleAttacks(Factions::AXIS, self::$table->getObjectListFromDB("SELECT * FROM pieces WHERE faction = 'germany' AND type IN ('INFANTRY','TANK') AND location in (SELECT location FROM pieces WHERE faction = 'germany' AND type = 'INFANTRY')"));
//
			case 58:
//
// Attack with a German force containing a tank
//
				return Pieces::getPossibleAttacks(Factions::AXIS, self::$table->getObjectListFromDB("SELECT * FROM pieces WHERE faction = 'germany' AND type IN ('INFANTRY','TANK') AND location in (SELECT location FROM pieces WHERE faction = 'germany' AND type = 'TANK')"));
//
			case 108:
//
				if ($action['name'] === 'eliminate') return self::$table->getObjectListFromDB("SELECT id FROM pieces WHERE faction = 'sovietUnion' AND type = 'INFANTRY' AND location in (SELECT location FROM pieces WHERE faction = 'sovietUnion' AND type = 'INFANTRY' GROUP BY location HAVING COUNT(*) >= 2);", true);
				if ($action['name'] === 'attack')
				{
					$lastPiece = null;
					$lastUndo = Actions::getLastUndo();
					if ($lastUndo)
					{
						$lastAction = Actions::get($lastUndo);
						if ($lastAction && array_key_exists('piece', $lastAction))
						{
							$lastPiece = $lastAction['piece'];
//
							$infanteries = [];
							foreach (Pieces::getAll(Factions::ALLIES) as $piece) if ($piece['type'] === Pieces::INFANTRY && $piece['location'] === $lastPiece['location']) $infanteries[] = $piece;
							return Pieces::getPossibleAttacks(Factions::ALLIES, $infanteries);
						}
					}
				}
				throw new BgaVisibleSystemException("Not implemented: $action[name]");
//
			default:
//
				throw new BgaVisibleSystemException("Not implemented: $action[special]");
		}
	}
}
