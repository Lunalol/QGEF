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
		$this->possible = ['move' => Pieces::getPossibleMoves($FACTION, Pieces::getAll($FACTION, 'moved', 'no'))];
//
		return ['FACTION' => $FACTION, 'move' => $this->possible['move'], 'cancel' => Actions::empty()];
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
//
		$playedLocations = Actions::getPlayedLocations();
		$playedPieces = Actions::getPlayedPieces();
//
		$action = Actions::get(Actions::getNextAction());
		switch ($action['name'])
		{
//
			case 'conscription':
//
				$control = Board::getControl($FACTION);
				$ennemies = Pieces::getEnnemyControled($FACTION);
//
				$this->possible = [];
				foreach (Factions::FACTIONS[$FACTION] as $faction)
				{
					foreach ([1 => [Pieces::INFANTRY], 2 => [Pieces::TANK, Pieces::AIRPLANE, Pieces::FLEET]][sizeof($action['cards'])] as $type)
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
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty() && !array_key_exists('noundo', $action), 'action' => $action, 'deploy' => $this->possible];
//
			case 'forcedMarch':
//
				$this->possible['move'] = Pieces::getPossibleMoves($FACTION, Pieces::getAll($FACTION));
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty() && !array_key_exists('noundo', $action), 'action' => $action, 'move' => $this->possible['move']];
//
			case 'desperateAttack':
//
				$this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, Pieces::getAll($FACTION));
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty() && !array_key_exists('noundo', $action), 'action' => $action, 'attack' => $this->possible['attack']];
//
			case 'deploy':
//
				$control = Board::getControl($FACTION);
				$ennemies = Pieces::getEnnemyControled($FACTION);
//
				$this->possible = [];
				foreach ($action['factions'] as $faction)
				{
					foreach ($action['types'] as $type)
					{
						if (array_key_exists('contain', $action)) foreach (Pieces::getAll($FACTION) as $piece) if (in_array($piece['faction'], $action['contain']['factions']) && in_array($piece['type'], $action['contain']['types'])) $action['locations'][] = intval($piece['location']);
						if (array_key_exists('control', $action)) $action['locations'] = Board::getControl($FACTION, $action['control']);
//
						$locations = [];
						foreach ($action['locations'] as $location)
						{
							if (array_key_exists('different', $action) && in_array($location, $playedLocations)) continue;
							if (array_key_exists('same', $action) && !in_array($location, $playedLocations)) continue;
							if (in_array($location, $control) && !in_array($location, $ennemies) && Board::REGIONS[$location]['type'] === WATER && $type === Pieces::FLEET) $locations[] = $location;
							if (in_array($location, $control) && !in_array($location, $ennemies) && Board::REGIONS[$location]['type'] === LAND && $type !== Pieces::FLEET) $locations[] = $location;
						}
						$this->possible[$faction][$type] = array_values(array_unique($locations));
					}
				}
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty() && !array_key_exists('noundo', $action), 'action' => $action, 'deploy' => $this->possible];
//
			case 'recruit':
//
				$ennemies = Pieces::getEnnemyControled($FACTION);
//
				$this->possible = [];
				foreach ($action['factions'] as $faction)
				{
					foreach ($action['types'] as $type)
					{
						if (array_key_exists('special', $action)) $action['locations'] = Decks::special($action);
						if (array_key_exists('contain', $action)) foreach (Pieces::getAll($FACTION) as $piece) if (in_array($piece['faction'], $action['contain']['factions']) && in_array($piece['type'], $action['contain']['types'])) $action['locations'][] = intval($piece['location']);
						if (array_key_exists('adjacent', $action))
						{
							foreach (Pieces::getAll($FACTION) as $piece)
							{
								if (in_array($piece['faction'], $action['adjacent']['factions']) && in_array($piece['type'], $action['adjacent']['types']))
								{
									foreach (Board::ADJACENCY[$piece['location']] as $next_location) $action['locations'][] = $next_location;
								}
							}
						}
//
						$locations = [];
						foreach ($action['locations'] as $location)
						{
							if (array_key_exists('different', $action) && in_array($location, $playedLocations)) continue;
							if (array_key_exists('same', $action) && !in_array($location, $playedLocations)) continue;
							if (!in_array($location, $ennemies) && Board::REGIONS[$location]['type'] === WATER && $type === Pieces::FLEET) $locations[] = $location;
							if (!in_array($location, $ennemies) && Board::REGIONS[$location]['type'] === LAND && $type !== Pieces::FLEET) $locations[] = $location;
						}
						$this->possible[$faction][$type] = array_values(array_unique($locations));
					}
				}
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty() && !array_key_exists('noundo', $action), 'action' => $action, 'deploy' => $this->possible];
//
			case 'move/attack':
//
				$this->possible['move'] = Pieces::getPossibleMoves($FACTION, $playedPieces);
//				if (array_key_exists('containing', $action)) $this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, $playedPieces);
				if (array_key_exists('containing', $action)) $this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => in_array($piece['location'], $playedLocations)));
				else $this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => in_array($piece['location'], $action['locations']) && in_array($piece['faction'], $action['factions'])));
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty() && !array_key_exists('noundo', $action), 'action' => $action, 'move' => $this->possible['move'], 'attack' => $this->possible['attack']];
//
			case 'move':
//
				if (array_key_exists('different', $action)) $this->possible['move'] = Pieces::getPossibleMoves($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => !array_key_exists($piece['id'], $playedPieces) && in_array($piece['type'], $action['types']) && in_array($piece['faction'], $action['factions'])));
				else if (array_key_exists('same', $action) && $playedPieces) $this->possible['move'] = Pieces::getPossibleMoves($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => array_key_exists($piece['id'], $playedPieces)));
				else $this->possible['move'] = Pieces::getPossibleMoves($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => in_array($piece['type'], $action['types']) && in_array($piece['faction'], $action['factions'])));
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty() && !array_key_exists('noundo', $action), 'action' => $action, 'move' => $this->possible['move']];
//
			case 'attack':
//
				if (array_key_exists('containing', $action) && array_key_exists('into', $action)) $this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => in_array($piece['location'], $playedLocations)), $action['into']);
				else if (array_key_exists('into', $action))
				{
					if ($action['into'] === true) $this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => in_array($piece['faction'], $action['factions'])), $playedLocations);
					else $this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => in_array($piece['faction'], $action['factions'])), $action['into']);
				}
				else if (array_key_exists('containing', $action)) $this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => in_array($piece['location'], $playedLocations)));
				else if (array_key_exists('locations', $action)) $this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => in_array($piece['location'], $action['locations']) && in_array($piece['faction'], $action['factions'])));

				if (array_key_exists('special', $action)) $this->possible['attack'] = Decks::special($action);
//
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty() && !array_key_exists('noundo', $action), 'action' => $action, 'attack' => $this->possible['attack']];
//
			case 'eliminate':
//
				$this->possible['pieces'] = [];
				if (array_key_exists('adjacent', $action))
				{
					foreach (Pieces::getAllDatas() as $piece)
					{
						if (in_array($piece['faction'], $action['adjacent']['factions']) && in_array($piece['type'], $action['adjacent']['types']))
						{
							foreach (Board::ADJACENCY[$piece['location']] as $next_location) $action['locations'][] = $next_location;
						}
					}
				}
				if (array_key_exists('range', $action) && $playedLocations) $this->possible['pieces'] = Pieces::getInRange($playedLocations[0], $action['range'], array_filter(Pieces::getAllDatas(), fn($piece) => in_array($piece['type'], $action['types']) && in_array($piece['faction'], $action['factions'])));
				if (array_key_exists('locations', $action)) foreach (Pieces::getAllDatas() as $piece) if (in_array($piece['location'], $action['locations']) && in_array($piece['type'], $action['types']) && in_array($piece['faction'], $action['factions'])) $this->possible['pieces'][] = $piece['id'];
				if (array_key_exists('special', $action)) $this->possible['pieces'] = Decks::special($action);
//
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty() && !array_key_exists('noundo', $action), 'action' => $action, 'eliminate' => $this->possible['pieces']];
//
			case 'eliminateVS':
//
				$this->possible['pieces'] = [];
				if (array_key_exists('range', $action) && $playedLocations)
				{
					$this->possible['pieces'] = Pieces::getInRange($playedLocations[0], $action['range'], array_filter(Pieces::getAllDatas(), fn($piece) => in_array($piece['type'], $action['types']) && in_array($piece['faction'], $action['factions'])));
				}
				if (array_key_exists('locations', $action)) foreach (Pieces::getAllDatas() as $piece) if (in_array($piece['location'], $action['locations']) && in_array($piece['type'], $action['types']) && in_array($piece['faction'], $action['factions'])) $this->possible['pieces'][] = $piece['id'];
				if (array_key_exists('special', $action)) $this->possible['pieces'] = Decks::special($action);
//
				return ['FACTION' => Factions::getInactive(), 'cancel' => false, 'action' => $action, 'eliminate' => $this->possible['pieces'], 'discard' => $action['discard'] ?? null, 'VP' => $action['VP'] ?? null];
//
			case 'scorched':
//
				$this->possible['scorched'] = $action['locations'];
//
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty() && !array_key_exists('noundo', $action), 'action' => $action, 'scorched' => $this->possible['scorched']];
//
			case 'discard':
//
				return ['FACTION' => $FACTION, 'action' => $action, 'discard' => $action['count']];
//
			default :
//
				return ['FACTION' => $FACTION, 'action' => $action];
		}
	}
	function argAttackRoundDefender()
	{
		$attackerFACTION = Factions::getActive();
		$defenderFACTION = Factions::getInactive();
//
		['location' => $location, 'faction' => $attackerfaction, 'from' => $from] = Factions::getStatus($attackerFACTION, 'attack');
//
		$attacker = array_keys(Pieces::getAtLocation($from, $attackerfaction));
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
					if ($attacker && $piece['player'] === $attackerFACTION && $piece['faction'] === $attackerfaction) $attacker[] = +$piece['id'];
					if ($defender && $piece['player'] === $defenderFACTION && in_array($piece['faction'], $defenderfactions)) $defender[] = +$piece['id'];
				}
			}
		}
//
		$this->possible = ['reactions' => [], 'pieces' => $defender];
//
		$removedPiece = Factions::getStatus($attackerFACTION, 'removedPiece');
		foreach ($this->decks->getPlayerHand($defenderFACTION) as $card)
		{
			if (!in_array(Decks::DECKS[$defenderFACTION][$card['type_arg']]['faction'], $defenderfactions)) continue;
			if (Decks::DECKS[$defenderFACTION][$card['type_arg']]['reaction'] === 'StandFast' && Decks::standFast($card['type_arg'], $location, $defender)) $this->possible['reactions'][] = +$card['id'];
			if (Decks::DECKS[$defenderFACTION][$card['type_arg']]['reaction'] === 'Retreat') $this->possible['reactions'][] = +$card['id'];
			if (Decks::DECKS[$defenderFACTION][$card['type_arg']]['reaction'] === 'Exchange') $this->possible['reactions'][] = +$card['id'];
			if (Decks::DECKS[$defenderFACTION][$card['type_arg']]['reaction'] === 'AntiAir' && $removedPiece && $removedPiece['type'] === 'airplane') $this->possible['reactions'][] = +$card['id'];
			if (Decks::DECKS[$defenderFACTION][$card['type_arg']]['reaction'] === 'NavalCombat' && $removedPiece && $removedPiece['type'] === 'fleet') $this->possible['reactions'][] = +$card['id'];
		}
//
		$this->possible['retreat'] = Pieces::getPossibleRetreats($defenderFACTION, $pieces);
//
		return ['FACTION' => $defenderFACTION, '_private' => [Factions::getPlayerID($defenderFACTION) => $this->possible],
			'location' => $location, 'attacker' => $attacker, 'defender' => $defender,
			'action' => Actions::get(Actions::getNextAction())];
	}
	function argAttackRoundAttacker()
	{
		$attackerFACTION = Factions::getActive();
		$defenderFACTION = Factions::getInactive();
//
		['location' => $location, 'faction' => $attackerfaction, 'from' => $from] = Factions::getStatus($attackerFACTION, 'attack');
//
		$attacker = array_keys(Pieces::getAtLocation($from, $attackerfaction));
		$pieces = Pieces::getAtLocation($location);
		$defenderfactions = array_unique(array_column($pieces, 'faction'));
//
		$supplyLines = Board::getSupplyLines($attackerFACTION);
//
		$defender = array_keys($pieces);
		foreach (Board::ADJACENCY[$location] as $next_location)
		{
			foreach (Pieces::getAtLocation($next_location) as $piece)
			{
				if ($piece['type'] === 'airplane' || $piece['type'] === 'fleet')
				{
					{
						if ($attacker && $piece['player'] === $attackerFACTION && $piece['faction'] === $attackerfaction)
//
// Unsupplied attacking airplanes and fleets cannot be used to continue an attack
//
							if (in_array($piece['location'], $supplyLines[$piece['faction']])) $attacker[] = +$piece['id'];
//
						if ($defender && $piece['player'] === $defenderFACTION && in_array($piece['faction'], $defenderfactions)) $defender[] = +$piece['id'];
					}
				}
			}
		}
//
		$action = Actions::get(Actions::getNextAction());
		if ($action && array_key_exists('special', $action) && $action['special'] === 62)
		{
			foreach (Pieces::getAtLocation($from, Factions::PACT) as $piece) $attacker[] = $piece['id'];
			foreach (Board::ADJACENCY[$location] as $next_location)
			{
				foreach (Pieces::getAtLocation($next_location, Factions::PACT) as $piece)
				{
					if ($piece['type'] === 'airplane' || $piece['type'] === 'fleet') $attacker[] = +$piece['id'];
				}
			}
		}
//
		$this->possible = ['reactions' => [], 'pieces' => $attacker];
//
		$removedPiece = Factions::getStatus($defenderFACTION, 'removedPiece');
		foreach ($this->decks->getPlayerHand($attackerFACTION) as $card)
		{
			if (in_array($card['id'], $action['cards'])) continue;
			if (Decks::DECKS[$attackerFACTION][$card['type_arg']]['faction'] !== $attackerfaction) continue;
			if (Decks::DECKS[$attackerFACTION][$card['type_arg']]['reaction'] === 'SustainAttack') $this->possible['reactions'][] = +$card['id'];
			if (Decks::DECKS[$attackerFACTION][$card['type_arg']]['reaction'] === 'AntiAir' && $removedPiece && $removedPiece['type'] === 'airplane') $this->possible['reactions'][] = +$card['id'];
			if (Decks::DECKS[$attackerFACTION][$card['type_arg']]['reaction'] === 'NavalCombat' && $removedPiece && $removedPiece['type'] === 'fleet') $this->possible['reactions'][] = +$card['id'];
		}
//
		return ['FACTION' => $attackerFACTION, '_private' => [Factions::getPlayerID($attackerFACTION) => $this->possible],
			'location' => $location, 'attacker' => $attacker, 'defender' => $defender, 'action' => $action];
	}
	function argAttackRoundExchange()
	{
		$attackerFACTION = Factions::getActive();
		$defenderFACTION = Factions::getInactive();
//
		['location' => $location, 'faction' => $attackerfaction, 'from' => $from] = Factions::getStatus($attackerFACTION, 'attack');
//
		$attacker = array_keys(Pieces::getAtLocation($from, $attackerfaction));
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
					if ($attacker && $piece['player'] === $attackerFACTION && $piece['faction'] === $attackerfaction) $attacker[] = +$piece['id'];
					if ($defender && $piece['player'] === $defenderFACTION && in_array($piece['faction'], $defenderfactions)) $defender[] = +$piece['id'];
				}
			}
		}
//
		$action = Actions::get(Actions::getNextAction());
		if ($action && array_key_exists('special', $action) && $action['special'] === 62)
		{
			foreach (Pieces::getAtLocation($from, Factions::PACT) as $piece) $attacker[] = $piece['id'];
			foreach (Board::ADJACENCY[$location] as $next_location)
			{
				foreach (Pieces::getAtLocation($next_location, Factions::PACT) as $piece)
				{
					if ($piece['type'] === 'airplane' || $piece['type'] === 'fleet') $attacker[] = +$piece['id'];
				}
			}
		}
//
		$this->possible = ['reactions' => [], 'pieces' => $attacker];
//
		return ['FACTION' => $attackerFACTION, '_private' => [Factions::getPlayerID($attackerFACTION) => $this->possible],
			'location' => $location, 'attacker' => $attacker, 'defender' => $defender,
			'action' => $action];
	}
	function argAttackRoundSpecial()
	{
		$attackerFACTION = Factions::getActive();
		$defenderFACTION = Factions::getInactive();
//
		['location' => $location, 'faction' => $attackerfaction, 'from' => $from] = Factions::getStatus($attackerFACTION, 'attack');
//
		$attacker = array_keys(Pieces::getAtLocation($from, $attackerfaction));
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
					if ($attacker && $piece['player'] === $attackerFACTION && $piece['faction'] === $attackerfaction) $attacker[] = +$piece['id'];
					if ($defender && $piece['player'] === $defenderFACTION && in_array($piece['faction'], $defenderfactions)) $defender[] = +$piece['id'];
				}
			}
		}
//
		$this->possible = ['reactions' => [], 'pieces' => $defender];
//
		$removedPiece = Factions::getStatus($attackerFACTION, 'removedPiece');
		foreach ($this->decks->getPlayerHand($defenderFACTION) as $card)
		{
			if (!in_array(Decks::DECKS[$defenderFACTION][$card['type_arg']]['faction'], $defenderfactions)) continue;
			if (Decks::DECKS[$defenderFACTION][$card['type_arg']]['reaction'] === 'AntiAir' && $removedPiece && $removedPiece['type'] === 'airplane') $this->possible['reactions'][] = +$card['id'];
			if (Decks::DECKS[$defenderFACTION][$card['type_arg']]['reaction'] === 'NavalCombat' && $removedPiece && $removedPiece['type'] === 'fleet') $this->possible['reactions'][] = +$card['id'];
		}
//
		return ['FACTION' => $defenderFACTION, '_private' => [Factions::getPlayerID($defenderFACTION) => $this->possible],
			'location' => $location, 'attacker' => $attacker, 'defender' => $defender, 'action' => Actions::get(Actions::getNextAction())];
	}
	function argAttackRoundAdvance()
	{
		$attackerFACTION = Factions::getActive();
//
		['location' => $location, 'faction' => $attackerfaction, 'from' => $from] = Factions::getStatus($attackerFACTION, 'attack');
//
		$attacker = array_keys(Pieces::getAtLocation($from, $attackerfaction));
		$this->possible = ['reactions' => [], 'pieces' => $attacker];
//
		$action = Actions::get(Actions::getNextAction());
//
		$free = array_key_exists('advance', $action);
		if (array_key_exists('requirement', $action) && $action['requirement'] === 'noSpringTurn') $free = $free && intval(self::getGameStateValue('round')) % 4 !== 0;
//		if (!$free)
		{
			if (!Factions::getStatus($attackerFACTION, 'mud'))
			{
				foreach ($this->decks->getPlayerHand($attackerFACTION) as $card)
				{
					if (in_array($card['id'], $action['cards'])) continue;
					if (Decks::DECKS[$attackerFACTION][$card['type_arg']]['faction'] !== $attackerfaction) continue;
					if (Decks::DECKS[$attackerFACTION][$card['type_arg']]['reaction'] === 'Advance') $this->possible['reactions'][] = +$card['id'];
				}
			}
		}
		if ($free)
		{
			$this->possible['reactions'][] = 0;
			$this->possible['free'] = array_keys(array_filter(Pieces::getAtLocation($from, $attackerfaction), fn($piece) => in_array($piece['type'], $action['advance'])));
		}
//
		return ['FACTION' => $attackerFACTION, '_private' => [Factions::getPlayerID($attackerFACTION) => $this->possible],
			'location' => $location, 'attacker' => $attacker, 'action' => $action];
	}
}
