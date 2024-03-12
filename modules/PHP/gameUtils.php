<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
trait gameUtils
{
	function discard()
	{
		$FACTION = Factions::getActive();
//
		$actions = Factions::getStatus($FACTION, 'action');
		$action = array_pop($actions);
//
		foreach ($action['cards'] as $id)
		{
			$this->{$FACTION . 'Deck'
			}->playCard($id);
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers($FACTION . 'Discard', clienttranslate('${FACTION} Discard 1 card'), ['card' => ['id' => $id], 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		}
	}
	function action()
	{
		$FACTION = Factions::getActive();
//
		$actions = Factions::getStatus($FACTION, 'action');
		array_pop($actions);
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
