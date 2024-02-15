<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class AxisDeck extends APP_GameClass
{
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
