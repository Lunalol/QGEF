<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class AlliesDeck extends APP_GameClass
{
	static function init()
	{
		$deck = self::getNew("module.common.deck");
		$deck->init("alliesDeck");
//
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
}
