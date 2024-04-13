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
		$action = Actions::get(Actions::action());
		switch ($action['name'])
		{
//
			case 'conscription':
//
//
				$control = Factions::getControl($FACTION);
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
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty(), 'action' => $action, 'deploy' => $this->possible];
//
			case 'forcedMarch':
//
				$this->possible['move'] = Pieces::getPossibleMoves($FACTION, Pieces::getAll($FACTION));
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty(), 'action' => $action, 'move' => $this->possible['move']];
//
			case 'desperateAttack':
//
				$this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, Pieces::getAll($FACTION));
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty(), 'action' => $action, 'attack' => $this->possible['attack']];
//
			case 'deploy':
//
				$control = Factions::getControl($FACTION);
				$ennemies = Pieces::getEnnemyControled($FACTION);
				$deployed = Factions::getStatus($FACTION, 'deployed');
//
				$this->possible = [];
				foreach ($action['factions'] as $faction)
				{
					foreach ($action['types'] as $type)
					{
						$locations = [];
//
						if ($action['locations'] === 'contain') $action['locations'] = array_unique(array_map('intval', array_column(Pieces::getAll($FACTION), 'location')));
//
						foreach ($action['locations'] as $location)
						{
							if (array_key_exists('different', $action) && $deployed && $deployed['location'] == $location) continue;
							if (in_array($location, $control) && !in_array($location, $ennemies) && Board::REGIONS[$location]['type'] === WATER && $type === Pieces::FLEET) $locations[] = $location;
							if (in_array($location, $control) && !in_array($location, $ennemies) && Board::REGIONS[$location]['type'] === LAND && $type !== Pieces::FLEET) $locations[] = $location;
						}
						$this->possible[$faction][$type] = array_values(array_unique($locations));
					}
				}
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty(), 'action' => $action, 'deploy' => $this->possible];
//
			case 'move/attack':
//
				$lastPiece = Actions::get(Actions::cancel())['piece'];
				$this->possible['move'] = Pieces::getPossibleMoves($FACTION, [$lastPiece]);
				if (array_key_exists('containing', $action))
				{
//					$this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => $piece['location'] === $deployed['location'] && $piece['faction'] === $deployed['faction']));
					$this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, [$lastPiece]);
				}
				else $this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => in_array($piece['location'], $action['locations']) && in_array($piece['faction'], $action['factions'])));
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty(), 'action' => $action, 'move' => $this->possible['move'], 'attack' => $this->possible['attack']];
//
			case 'move':
//
				$this->possible['move'] = Pieces::getPossibleMoves($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => in_array($piece['type'], $action['types']) && in_array($piece['faction'], $action['factions'])));
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty(), 'action' => $action, 'move' => $this->possible['move']];
//
			case 'attack':
//
				if (array_key_exists('containing', $action))
				{
					$lastPiece = Actions::get(Actions::cancel())['piece'];
					$location = Pieces::getLocation($lastPiece['id']);
					$this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => $piece['location'] === $location && $piece['faction'] === $lastPiece['faction']));
				}
				else $this->possible['attack'] = Pieces::getPossibleAttacks($FACTION, array_filter(Pieces::getAll($FACTION), fn($piece) => in_array($piece['location'], $action['locations']) && in_array($piece['faction'], $action['factions'])));
//
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty(), 'action' => $action, 'attack' => $this->possible['attack']];
//
			case 'eliminate':
//
				$lastPiece = Actions::get(Actions::cancel())['piece'];
				$this->possible['pieces'] = Pieces::getInRange($lastPiece['location'], $action['range'], array_filter(Pieces::getAllDatas(), fn($piece) => in_array($piece['type'], $action['types']) && $piece['player'] !== $FACTION));
				return ['FACTION' => $FACTION, 'cancel' => Actions::empty(), 'action' => $action, 'eliminate' => $this->possible['pieces']];
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
		['location' => $location, 'faction' => $attackerfaction, 'pieces' => $attacker] = Factions::getStatus($attackerFACTION, 'attack');
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
					if ($attacker && $piece['player'] === $attackerFACTION && $piece['faction'] === $attackerfaction) $attacker[] = +$piece['id'];
					if ($defender && $piece['player'] === $defenderFACTION && in_array($piece['faction'], $defenderfactions)) $defender[] = +$piece['id'];
				}
			}
		}
//
		$this->possible = ['reactions' => [], 'pieces' => $defender];
//
		$class = "${defenderFACTION}Deck";
		$removedPiece = Factions::getStatus($attackerFACTION, 'removedPiece');
		foreach ($this->{$defenderFACTION . 'Deck'}->getPlayerHand($defenderFACTION) as $card)
		{
			if (!in_array($class::DECK[$card['type_arg']]['faction'], $defenderfactions)) continue;
			if ($class::DECK[$card['type_arg']]['reaction'] === 'StandFast' && $class::standFast($card['type_arg'], $location, $defender)) $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'Retreat') $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'Exchange') $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'AntiAir' && $removedPiece && $removedPiece['type'] === 'airplane') $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'NavalCombat' && $removedPiece && $removedPiece['type'] === 'fleet') $this->possible['reactions'][] = +$card['id'];
		}
//
		$this->possible['retreat'] = Pieces::getPossibleRetreats($defenderFACTION, $pieces);
//
		return ['FACTION' => $defenderFACTION, '_private' => [Factions::getPlayerID($defenderFACTION) => $this->possible],
			'location' => $location, 'attacker' => $attacker, 'defender' => $defender];
	}
	function argAttackRoundAttacker()
	{
		$attackerFACTION = Factions::getActive();
		$defenderFACTION = Factions::getInactive();
//
		['location' => $location, 'faction' => $attackerfaction, 'pieces' => $attacker] = Factions::getStatus($attackerFACTION, 'attack');
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
					if ($attacker && $piece['player'] === $attackerFACTION && $piece['faction'] === $attackerfaction) $attacker[] = +$piece['id'];
					if ($defender && $piece['player'] === $defenderFACTION && in_array($piece['faction'], $defenderfactions)) $defender[] = +$piece['id'];
				}
			}
		}
//
		$this->possible = ['reactions' => [], 'pieces' => $attacker];
//
		$class = "${attackerFACTION}Deck";
		$removedPiece = Factions::getStatus($defenderFACTION, 'removedPiece');
		foreach ($this->{$attackerFACTION . 'Deck'}->getPlayerHand($attackerFACTION) as $card)
		{
			if ($class::DECK[$card['type_arg']]['faction'] !== $attackerfaction) continue;
			if ($class::DECK[$card['type_arg']]['reaction'] === 'SustainAttack') $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'AntiAir' && $removedPiece && $removedPiece['type'] === 'airplane') $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'NavalCombat' && $removedPiece && $removedPiece['type'] === 'fleet') $this->possible['reactions'][] = +$card['id'];
		}
//
		return ['FACTION' => $attackerFACTION, '_private' => [Factions::getPlayerID($attackerFACTION) => $this->possible],
			'location' => $location, 'attacker' => $attacker, 'defender' => $defender];
	}
	function argAttackRoundExchange()
	{
		$attackerFACTION = Factions::getActive();
		$defenderFACTION = Factions::getInactive();
//
		['location' => $location, 'faction' => $attackerfaction, 'pieces' => $attacker] = Factions::getStatus($attackerFACTION, 'attack');
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
					if ($attacker && $piece['player'] === $attackerFACTION && $piece['faction'] === $attackerfaction) $attacker[] = +$piece['id'];
					if ($defender && $piece['player'] === $defenderFACTION && in_array($piece['faction'], $defenderfactions)) $defender[] = +$piece['id'];
				}
			}
		}
//
		$this->possible = ['reactions' => [], 'pieces' => $attacker];
//
		return ['FACTION' => $attackerFACTION, '_private' => [Factions::getPlayerID($attackerFACTION) => $this->possible],
			'location' => $location, 'attacker' => $attacker, 'defender' => $defender];
	}
	function argAttackRoundSpecial()
	{
		$attackerFACTION = Factions::getActive();
		$defenderFACTION = Factions::getInactive();
//
		['location' => $location, 'faction' => $attackerfaction, 'pieces' => $attacker] = Factions::getStatus($attackerFACTION, 'attack');
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
					if ($attacker && $piece['player'] === $attackerFACTION && $piece['faction'] === $attackerfaction) $attacker[] = +$piece['id'];
					if ($defender && $piece['player'] === $defenderFACTION && in_array($piece['faction'], $defenderfactions)) $defender[] = +$piece['id'];
				}
			}
		}
//
		$this->possible = ['reactions' => [], 'pieces' => $defender];
//
		$class = "${defenderFACTION}Deck";
		$removedPiece = Factions::getStatus($attackerFACTION, 'removedPiece');
		foreach ($this->{$defenderFACTION . 'Deck'}->getPlayerHand($defenderFACTION) as $card)
		{
			if (!in_array($class::DECK[$card['type_arg']]['faction'], $defenderfactions)) continue;
			if ($class::DECK[$card['type_arg']]['reaction'] === 'AntiAir' && $removedPiece && $removedPiece['type'] === 'airplane') $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'NavalCombat' && $removedPiece && $removedPiece['type'] === 'fleet') $this->possible['reactions'][] = +$card['id'];
		}
//
		return ['FACTION' => $defenderFACTION, '_private' => [Factions::getPlayerID($defenderFACTION) => $this->possible],
			'location' => $location, 'attacker' => $attacker, 'defender' => $defender];
	}
	function argAttackRoundAdvance()
	{
		$attackerFACTION = Factions::getActive();
//
		['location' => $location, 'faction' => $attackerfaction, 'pieces' => $attacker] = Factions::getStatus($attackerFACTION, 'attack');
//
		$this->possible = ['pieces' => $attacker];
//
		$class = "${attackerFACTION}Deck";
		foreach ($this->{$attackerFACTION . 'Deck'}->getPlayerHand($attackerFACTION) as $card)
		{
			if ($class::DECK[$card['type_arg']]['faction'] !== $attackerfaction) continue;
			if ($class::DECK[$card['type_arg']]['reaction'] === 'Advance') $this->possible['reactions'][] = +$card['id'];
		}
//
		return ['FACTION' => $attackerFACTION, '_private' => [Factions::getPlayerID($attackerFACTION) => $this->possible],
			'location' => $location, 'attacker' => $attacker];
	}
}
