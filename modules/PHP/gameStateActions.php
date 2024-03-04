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
	function acCancel(): void
	{
		$this->checkAction('cancel');
//
		$FACTION = Factions::getActive();
		Factions::setStatus($FACTION, 'action');
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '${FACTION} Last action canceled', ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('cancel');
	}
	function acPlay(array $cards): void
	{
		$this->checkAction('play');
//
		if (sizeof($cards) !== 1) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		$FACTION = Factions::getActive();
		Factions::setStatus($FACTION, 'action', [['name' => 'play', 'cards' => $cards]]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '${FACTION} <B>Play</B>', ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('action');
	}
	function acConscription(array $cards): void
	{
		$this->checkAction('conscription');
//
		if (sizeof($cards) < 1 || sizeof($cards) > 2) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		$FACTION = Factions::getActive();
		Factions::setStatus($FACTION, 'action', [['name' => 'conscription', 'cards' => $cards]]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '${FACTION} <B>Conscription</B>', ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('action');
	}
	function acForcedMarch(array $cards): void
	{
		$this->checkAction('forcedMarch');
//
		if (sizeof($cards) !== 1) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		$FACTION = Factions::getActive();
		Factions::setStatus($FACTION, 'action', [['name' => 'forcedMarch', 'cards' => $cards]]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '${FACTION} <B>Forced March</B>', ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('action');
	}
	function acDesperateAttack(array $cards): void
	{
		$this->checkAction('desperateAttack');
//
		if (sizeof($cards) !== 2) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		$FACTION = Factions::getActive();
		Factions::setStatus($FACTION, 'action', [['name' => 'desperateAttack', 'cards' => $cards]]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '${FACTION} <B>Desperate Attack</B>', ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('action');
	}
	function acProductionInitiative(): void
	{
		$this->checkAction('productionInitiative');
//
		$FACTION = Factions::getActive();
		$card = $this->{$FACTION . 'Deck'}->pickCard('deck', $FACTION);
//* -------------------------------------------------------------------------------------------------------- */
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '${FACTION} <B>Production Initiative</B>', ['FACTION' => $FACTION]);
		self::notifyAllPlayers($FACTION . 'Deck', '${FACTION} Draw 1 card', ['card' => $card, 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('next');
	}
	function acContingency(array $cards): void
	{
		$this->checkAction('play');
//
		if (sizeof($cards) !== 1) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		$FACTION = Factions::getActive();
		Factions::setStatus($FACTION, 'action', [['name' => 'contingency', 'cards' => $cards]]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '${FACTION} <B>Contingency</B>', ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('action');
	}
	function acDeploy(string $location, string $faction, string $type): void
	{
		$this->checkAction('deploy');
//
		if (!array_key_exists($faction, $this->possible)) throw new BgaVisibleSystemException("Invalid faction: $faction");
		if (!array_key_exists($type, $this->possible[$faction])) throw new BgaVisibleSystemException("Invalid type: $type");
		if (!in_array($location, $this->possible[$faction][$type])) throw new BgaVisibleSystemException("Invalid location: $location");
//
		$FACTION = Factions::getActive();
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('placePiece', '${faction} <B>${type}</B> deploys at <B>${location}</B>', [
			'faction' => $faction, 'type' => $this->PIECES[$type],
			'location' => $this->REGIONS[$location], 'i18n' => ['type', 'location'],
			'piece' => Pieces::get(Pieces::create($FACTION, $faction, $type, $location))]);
//* -------------------------------------------------------------------------------------------------------- */
		self::action($FACTION);
	}
	function acMove(string $location, array $pieces): void
	{
		$this->checkAction('move');
//
		$FACTION = Factions::getActive();
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
			$old_location = $piece['location'];
			$piece['location'] = $location;
			Pieces::setLocation($id, $location);
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers('placePiece', '${faction} <B>${type}</B> moves from <B>${old}</B> to <B>${new}</B>', [
				'faction' => $piece['faction'], 'type' => $this->PIECES[$piece['type']],
				'old' => $this->REGIONS[$old_location], 'new' => $this->REGIONS[$location],
				'i18n' => ['type', 'old', 'new'],
				'piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
		}
//
		if ($this->gamestate->state()['name'] === 'action') self::action($FACTION);
		else $this->gamestate->nextState('continue');
	}
	function acAttack(string $location, array $pieces): void
	{
		$this->checkAction('attack');
//
		$FACTION = Factions::getActive();
//
// Check attack
//
		$faction = null;
		foreach ($pieces as $id)
		{
			if (!array_key_exists($id, $this->possible)) throw new BgaVisibleSystemException("Invalid piece: $id");
			if (!in_array($location, $this->possible[$id])) throw new BgaVisibleSystemException("Invalid location: $location");
//
			$piece = Pieces::get($id);
			if (is_null($faction)) $faction = $piece['faction'];
			if ($faction !== $piece['faction']) throw new BgaVisibleSystemException("Invalid faction");
		}
//
// Do attack
//
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '${faction} Attack on <B>${location}</B>', [
			'faction' => $faction, 'location' => $this->REGIONS[$location], 'i18n' => ['location']]);
//* -------------------------------------------------------------------------------------------------------- */
		Factions::setStatus($FACTION, 'attack', ['location' => $location, 'faction' => $faction, 'pieces' => $pieces]);
		self::setGameStateValue('rank', 0);
//
		$this->gamestate->nextState('attack');
	}
	function acRemovePiece(int $pieceID): void
	{
		$this->checkAction('removePiece');
//
// Check remove piece
//
		$piece = Pieces::get($pieceID);
		if (!$piece) throw new BgaVisibleSystemException("Invalid piece: $pieceID");
//
		if (!array_key_exists('pieces', $this->possible)) throw new BgaVisibleSystemException("Invalid possible: " . json_encode($this->possible));
		if (!in_array($pieceID, $this->possible['pieces'])) throw new BgaVisibleSystemException("Invalid piece: $pieceID");
//
		$rank = Pieces::RANK[$piece['type']];
		if ($this->gamestate->state()['name'] === 'attackRoundAttacker')
		{
			if ($rank < self::getGameStateValue('rank'))
			{
				switch (self::getGameStateValue('rank'))
				{
					case 1:
						throw new BgaUserException(self::_('You must remove a <B>Tank</B>, an Airplane, a Fleet or react with Sustain Attack to initiate a new round of combat'));
					case 2:
						throw new BgaUserException(self::_('You must remove an Airplane, a Fleet or react with Sustain Attack to initiate a new round of combat'));
					case 3:
						throw new BgaUserException(self::_('You must react with Sustain Attack to initiate a new round of combat'));
				}
			}
		}
//
// Do remove piece
//
		self::setGameStateValue('rank', $rank);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('removePiece', '', ['piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
		Pieces::destroy($pieceID);
//
		$this->gamestate->nextState('continue');
	}
	function acReaction(int $id): void
	{
		$this->checkAction('reaction');
//
// Check reaction
//
		if (!array_key_exists('reactions', $this->possible)) throw new BgaVisibleSystemException("Invalid possible: " . json_encode($this->possible));
		if (!in_array($id, $this->possible['reactions'])) throw new BgaVisibleSystemException("Invalid card: $id");
//
// Do reaction
//
		$attackerFACTION = Factions::getActive();
		$defenderFACTION = Factions::getInactive();
//
		['location' => $location, 'faction' => $attackerfaction, 'pieces' => $attacker] = Factions::getStatus($attackerFACTION, 'attack');
//
		$state = $this->gamestate->state()['name'];
		switch ($state)
		{
//
			case 'attackRoundAttacker':
//
				$card = $this->{$attackerFACTION . 'Deck'}->getCard($id);
				if (!$card) throw new BgaVisibleSystemException("Invalid attacker card: $id");
//
				$class = "${$attackerFACTION}Deck";
//
				$reaction = $class::DECK[$card['type_arg']]['reaction'];
				switch ($reaction)
				{
					case 'sustainAttack':
						break;
					default:
						throw new BgaVisibleSystemException("Invalid reaction for attacker: $reaction");
				}
//
				break;
//
			case 'attackRoundDefender':
//
				$card = $this->{$defenderFACTION . 'Deck'}->getCard($id);
				if (!$card) throw new BgaVisibleSystemException("Invalid defender card: $id");
//
				$class = "${defenderFACTION}Deck";
//
				$reaction = $class::DECK[$card['type_arg']]['reaction'];
				switch ($reaction)
				{
					case 'standFast':
						break;
					default:
						throw new BgaVisibleSystemException("Invalid reaction for defender: $reaction");
				}
//
				break;
//
			default:
//
				throw new BgaVisibleSystemException("Invalid game state for reaction: $state");
//
		}
		die;
	}
}
