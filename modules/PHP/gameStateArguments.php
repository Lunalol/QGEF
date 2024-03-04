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
		$attackerFACTION = Factions::getActive();
		$defenderFACTION = Factions::getInactive();
		$player_id = Factions::getPlayerID($defenderFACTION);
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
		foreach ($this->{$defenderFACTION . 'Deck'}->getPlayerHand($defenderFACTION) as $card)
		{
			if ($class::DECK[$card['type_arg']]['reaction'] === 'StandFast' && $class::standFast($card['type_arg'], $location, $defender)) $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'Retreat') $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'Exchange') $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'AntiAir') $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'NavalCombat') $this->possible['reactions'][] = +$card['id'];
		}
//
		return ['FACTION' => $defenderFACTION, '_private' => [$player_id => $this->possible],
			'location' => $location, 'attacker' => $attacker, 'defender' => $defender];
	}
	function argAttackRoundAttacker()
	{
		$attackerFACTION = Factions::getActive();
		$defenderFACTION = Factions::getInactive();
		$player_id = Factions::getPlayerID($attackerFACTION);
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
					if ($attacker && $piece['player'] === $attackerFACTION && $piece['faction'] === $attackerfaction) $attacker[] = $piece['id'];
					if ($defender && $piece['player'] === $defenderFACTION && in_array($piece['faction'], $defenderfactions)) $defender[] = $piece['id'];
				}
			}
		}
//
		$this->possible = ['reactions' => [], 'pieces' => $attacker];
//
		$class = "${attackerFACTION}Deck";
		foreach ($this->{$attackerFACTION . 'Deck'}->getPlayerHand($attackerFACTION) as $card)
		{
			if ($class::DECK[$card['type_arg']]['reaction'] === 'SustainAttack') $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'AntiAir') $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'NavalCombat') $this->possible['reactions'][] = +$card['id'];
			if ($class::DECK[$card['type_arg']]['reaction'] === 'Advance') $this->possible['reactions'][] = +$card['id'];
		}
		return ['FACTION' => $attackerFACTION, '_private' => [$player_id => $this->possible],
			'location' => $location, 'attacker' => $attacker, 'defender' => $defender];
	}
}
