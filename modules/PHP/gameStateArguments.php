<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
trait gameStateArguments
{
	function argMovement()
	{
		$FACTION = Factions::getActive();
		$this->possible = Pieces::getPossibleMoves($FACTION, Pieces::getAll($FACTION, 'moved', 'no'));
//
		return ['FACTION' => $FACTION, 'move' => $this->possible];
	}
	function argAction()
	{
		$FACTION = Factions::getActive();
//
		return ['FACTION' => $FACTION];
	}
}
