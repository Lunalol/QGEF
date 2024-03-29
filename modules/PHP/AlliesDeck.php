<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class AlliesDeck extends APP_GameClass
{
	const DECK = [
		8 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Advance'],
		9 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'AntiAir'],
		10 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'SustainAttack'],
		11 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'StandFast'],
		12 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'NavalCombat'],
		13 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange'],
		14 => ['faction' => Factions::SOVIETUNION, 'reaction' => 'Exchange'],
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
		for ($index = 8;
			$index <= 14;
			$index++) $DECK[] = ['type' => FIRST_GAME, 'type_arg' => $index, 'nbr' => 1];
		for ($index = 34;
			$index <= 48;
			$index++) $DECK[] = ['type' => MID, 'type_arg' => $index, 'nbr' => 1];
		$deck->createCards($DECK, 'deck');
//
		$ASIDE = [];
		for ($index = 72;
			$index <= 100;
			$index++) $ASIDE[] = ['type' => LATE, 'type_arg' => $index, 'nbr' => 1];
		$deck->createCards($ASIDE, 'aside');
//
// ➤ 5 Soviet Union Contingency cards
//
		$CONTINGENCY = [];
		for ($index = 106;
			$index <= 110;
			$index++) $CONTINGENCY[] = ['type' => INITIAL_SIDE, 'type_arg' => $index, 'nbr' => 1];
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
