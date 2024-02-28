<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class AxisDeck extends APP_GameClass
{
	const DECK = [
		1 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack'],
		2 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack'],
		3 => ['faction' => Factions::GERMANY, 'reaction' => 'SustainAttack'],
		4 => ['faction' => Factions::GERMANY, 'reaction' => 'AntiAir'],
		5 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance'],
		6 => ['faction' => Factions::GERMANY, 'reaction' => 'Advance'],
		7 => ['faction' => Factions::PACT, 'reaction' => 'SustainAttack'],
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
	];
//
	static function init()
	{
		$deck = self::getNew("module.common.deck");
		$deck->init("axisDeck");
//
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
}
