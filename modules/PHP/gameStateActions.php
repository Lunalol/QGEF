<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
trait gameStateActions
{
	function acPass(): void
	{
		$this->checkAction('pass');
//
		Pieces::setStatus(Pieces::ALL, 'moved');
//
		$this->gamestate->nextState('next');
	}
	function acMove(string $location, array $pieces): void
	{
		$this->checkAction('move');
//
//		$FACTION = Factions::getActive();
//
// Check movement
//
		foreach ($pieces as $id)
		{
			if (!array_key_exists($id, $this->possible)) throw new BgaVisibleSystemException("Invalid piece: $id");
			if (!in_array($location, $this->possible[$id])) throw new BgaVisibleSystemException("Invalid location: $location");
		}
//
// Do movement
//
		foreach ($pieces as $id)
		{
			$piece = Pieces::get($id);
			if ($piece['location'] !== $location) Pieces::setStatus($id, 'moved');
//
			$piece['location'] = $location;
			Pieces::setLocation($id, $location);
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers('placePiece', '', ['piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
		}
//
		$this->gamestate->nextState('continue');
	}
	function acProductionInitiative(): void
	{
		$this->checkAction('productionInitiative');
//
		$FACTION = Factions::getActive();
//
		$card = $this->{$FACTION . 'Deck'}->pickCard('deck', $FACTION);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers($FACTION . 'Deck', '<B>Production Initiative</B>:<BR>${faction} Draw 1 card', ['card' => $card, 'faction' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
//
		$this->gamestate->nextState('next');
	}
}
