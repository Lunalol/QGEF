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
			$card = $this->decks->getCard($cardID);
//
			switch ($card['type'])
			{
//
				case Decks::INITIAL_SIDE:
//
					self::dBquery("UPDATE decks SET card_type = " . Decks::SECOND_SIDE . " WHERE card_id = $card[id]");
//* -------------------------------------------------------------------------------------------------------- */
					self::notifyAllPlayers('flip', clienttranslate('${FACTION} Flips contingency card'), ['card' => ['id' => $cardID], 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
					break;
//
				case Decks::SECOND_SIDE:
//
					$this->decks->moveCard($cardID, 'discard', $FACTION);
//* -------------------------------------------------------------------------------------------------------- */
					self::notifyAllPlayers('discard', clienttranslate('${FACTION} Discards contingency card'), ['card' => ['id' => $cardID], 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
					break;
//
				default:
//
					$this->decks->moveCard($cardID, 'discard', $FACTION);
//* -------------------------------------------------------------------------------------------------------- */
					self::notifyAllPlayers($FACTION . 'Discard', clienttranslate('${FACTION} Discards 1 card'), ['card' => ['id' => $cardID], 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
					break;
//
			}
		}
	}
	function action(bool $pass = false)
	{
//PJL
		self::setGameStateValue('action', 1);
//PJL
		$id = Actions::getNextAction();
		if ($id)
		{
			$action = Actions::get($id);
//
			if (array_key_exists('infinite', $action) && !$pass) return $this->gamestate->nextState('action');
//
			Actions::setStatus($id, 'done');
			if (!$pass && array_key_exists('trigger', $action))
			{
				$action['trigger']['cards'] = $action['cards'];
				Actions::add('pending', $action['trigger']);
			}
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
