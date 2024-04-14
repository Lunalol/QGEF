<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
trait gameUtils
{
	function discard($cards)
	{
		$FACTION = Factions::getActive();
//
		foreach ($cards as $cardID)
		{
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers($FACTION . 'Discard', clienttranslate('${FACTION} Discard 1 card'), ['card' => ['id' => $cardID], 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
			$this->{$FACTION . 'Deck'}->playCard($cardID);
		}
	}
	function action()
	{
		$id = Actions::getNextAction();
		if ($id)
		{
			$action = Actions::get($id);
			Actions::setStatus($id, 'done');
//
			if (!Actions::getNextAction())
			{
				self::discard($action['cards']);
				self::incGameStateValue('action', 1);
				$this->gamestate->nextState('next');
			}
			else $this->gamestate->nextState('action');
		}
		else $this->gamestate->nextState('continue');
	}
}
