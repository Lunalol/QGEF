<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
trait gameStateArguments
{
	function argMovementStep()
	{
		$FACTION = Factions::getActive();
		$this->possible = Pieces::getPossibleMoves($FACTION, Pieces::getAll($FACTION, 'moved', 'no'));
//
		return ['FACTION' => $FACTION, 'move' => $this->possible];
	}
	function argActionStep()
	{
		$FACTION = Factions::getActive();
//
		return ['FACTION' => $FACTION];
	}
	function argAction()
	{
		$FACTION = Factions::getActive();
		$action = Factions::getStatus($FACTION, 'action');
//
		switch ($action['name'])
		{
//
			case 'conscription':
//
				$control = Factions::getControl($FACTION);
				$ennemies = Pieces::getEnnemyControled($FACTION);
//
				$this->possible = [];
				foreach (Factions::FACTIONS[$FACTION] as $faction)
				{
					foreach ([1 => [Pieces::INFANTERY], 2 => [Pieces::TANK, Pieces::AIRPLANE, Pieces::FLEET]][sizeof($action['cards'])] as $type)
					{
						$locations = [];
						foreach (Board::SUPPLY[$faction] as $location)
						{
							if (in_array($location, $control) && !in_array($location, $ennemies) && Board::REGIONS[$location]['type'] === WATER && $type === Pieces::FLEET) $locations[] = $location;
							if (in_array($location, $control) && !in_array($location, $ennemies) && Board::REGIONS[$location]['type'] === LAND && $type !== Pieces::FLEET) $locations[] = $location;
//
							foreach (Board::ADJACENCY[$location] as $location)
							{
								if (in_array($location, $control) && !in_array($location, $ennemies) && Board::REGIONS[$location]['type'] === WATER && $type === Pieces::FLEET) $locations[] = $location;
								if (in_array($location, $control) && !in_array($location, $ennemies) && Board::REGIONS[$location]['type'] === LAND && $type !== Pieces::FLEET) $locations[] = $location;
							}
						}
						$this->possible[$faction][$type] = array_values(array_unique($locations));
					}
				}
				return ['FACTION' => $FACTION, 'action' => $action, 'deploy' => $this->possible];
//
			case 'forcedMarch':
//
				$this->possible = Pieces::getPossibleMoves($FACTION, Pieces::getAll($FACTION));
				return ['FACTION' => $FACTION, 'action' => $action, 'move' => $this->possible];
//
			case 'desperateAttack':
//
				$this->possible = [];
				return ['FACTION' => $FACTION, 'action' => $action, 'attack' => $this->possible];
//
		}
	}
}
