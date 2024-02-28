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
		return ['FACTION' => $FACTION, 'action' => [1 => clienttranslate('first'), 2 => clienttranslate('second')][self::getGameStateValue('action')]];
	}
	function argAction()
	{
		$FACTION = Factions::getActive();
		$actions = Factions::getStatus($FACTION, 'action');
		$action = array_pop($actions);
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
				$this->possible = Pieces::getPossibleAttacks($FACTION, Pieces::getAll($FACTION));
				return ['FACTION' => $FACTION, 'action' => $action, 'attack' => $this->possible];
//
		}
	}
	function argAttackRoundDefender()
	{
		$FACTION = Factions::getActive();
		['location' => $location, 'faction' => $attackerfaction, 'pieces' => $attacker] = Factions::getStatus($FACTION, 'attack');
//
		$pieces = Pieces::getAtLocation($location);
		$defenderfactions = array_unique(array_column($pieces, 'faction'));
//
		$defender = array_keys($pieces);
		foreach (Board::ADJACENCY[$location] as $next_location)
		{
			foreach (Pieces::getAtLocation($next_location) as $piece)
			{
				if ($piece['type'] === 'airplane' || $piece['type'] === 'fleet')
				{
					if ($attacker && $piece['player'] === $FACTION && $piece['faction'] === $attackerfaction) $attacker[] = $piece['id'];
					if ($defender && $piece['player'] !== $FACTION && in_array($piece['faction'], $defenderfactions)) $defender[] = $piece['id'];
				}
			}
		}
//
		$this->possible = $defender;
//
		return ['FACTION' => $FACTION, 'location' => $location, 'attacker' => $attacker, 'defender' => $defender];
	}
	function argAttackRoundAttacker()
	{
		$FACTION = Factions::getActive();
		['location' => $location, 'faction' => $attackerfaction, 'pieces' => $attacker] = Factions::getStatus($FACTION, 'attack');
//
		$pieces = Pieces::getAtLocation($location);
		$defenderfactions = array_unique(array_column($pieces, 'faction'));
//
		$defender = array_keys($pieces);
		foreach (Board::ADJACENCY[$location] as $next_location)
		{
			foreach (Pieces::getAtLocation($next_location) as $piece)
			{
				if ($piece['type'] === 'airplane' || $piece['type'] === 'fleet')
				{
					if ($attacker && $piece['player'] === $FACTION && $piece['faction'] === $attackerfaction) $attacker[] = $piece['id'];
					if ($defender && $piece['player'] !== $FACTION && in_array($piece['faction'], $defenderfactions)) $defender[] = $piece['id'];
				}
			}
		}
//
		$this->possible = $attacker;
//
		return ['FACTION' => $FACTION, 'location' => $location, 'attacker' => $attacker, 'defender' => $defender];
	}
}
