<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
trait gameUtils
{
	function action($FACTION)
	{
		$actions = Factions::getStatus($FACTION, 'action');
		$action = array_pop($actions);
//
		foreach ($action['cards'] as $card)
		{
			$this->{$FACTION . 'Deck'}->moveCard($card, 'discard', $FACTION);
			self::notifyAllPlayers($FACTION . 'Discard', '${faction} Discard 1 card', ['card' => $card, 'FACTION' => $FACTION, 'faction' => $FACTION]);
		}
//
		if ($actions)
		{
			Factions::setStatus($FACTION, 'action', $actions);
			$this->gamestate->nextState('action');
		}
		else
		{
			Factions::setStatus($FACTION, 'action');
			$this->gamestate->nextState('next');
		}
	}
}
