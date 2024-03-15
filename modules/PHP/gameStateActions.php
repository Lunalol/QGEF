<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
trait gameStateActions
{
	function acPass(string $FACTION): void
	{
		$this->checkAction('pass');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
//
		Pieces::setStatus(Pieces::ALL, 'moved');
//
		if ($this->gamestate->state()['name'] === 'attackRoundAdvance')
		{
			$this->gamestate->nextState('end');
			self::action();
		}
		else $this->gamestate->nextState('next');
	}
	function acCancel(string $FACTION): void
	{
		$this->checkAction('cancel');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
//
		Factions::setStatus($FACTION, 'action');
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', clienttranslate('${FACTION} Last action canceled'), ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('cancel');
	}
	function acPlay(string $FACTION, array $cards): void
	{
//
// Check Play
//
		$this->checkAction('play');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
		if (sizeof($cards) !== 1) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		Factions::setStatus($FACTION, 'action', [['name' => 'play', 'cards' => $cards]]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', clienttranslate('${FACTION} <B>Play</B>'), ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('action');
	}
	function acConscription(string $FACTION, array $cards): void
	{
//
// Check Conscription
//
		$this->checkAction('conscription');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
		if (sizeof($cards) < 1 || sizeof($cards) > 2) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		Factions::setStatus($FACTION, 'action', [['name' => 'conscription', 'cards' => $cards]]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', clienttranslate('${FACTION} <B>Conscription</B>'), ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('action');
	}
	function acForcedMarch(string $FACTION, array $cards): void
	{
//
// Check ForcedMarch
//
		$this->checkAction('forcedMarch');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
		if (sizeof($cards) !== 1) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		Factions::setStatus($FACTION, 'action', [['name' => 'forcedMarch', 'cards' => $cards]]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', clienttranslate('${FACTION} <B>Forced March</B>'), ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('action');
	}
	function acDesperateAttack(string $FACTION, array $cards): void
	{
//
// Check DesperateAttack
//
		$this->checkAction('desperateAttack');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
		if (sizeof($cards) !== 2) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		Factions::setStatus($FACTION, 'action', [['name' => 'desperateAttack', 'cards' => $cards]]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', clienttranslate('${FACTION} <B>Desperate Attack</B>'), ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('action');
	}
	function acProductionInitiative(string $FACTION): void
	{
//
// Check ProductionInitiative
//
		$this->checkAction('productionInitiative');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
//
		$card = $this->{$FACTION . 'Deck'}->pickCard('deck', $FACTION);
		if (!$card) throw new BgaUserException(self::_('Your deck is empty'));
//* -------------------------------------------------------------------------------------------------------- */
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', clienttranslate('${FACTION} <B>Production Initiative</B>'), ['FACTION' => $FACTION]);
		self::notifyAllPlayers('msg', clienttranslate('${FACTION} Draw 1 card'), ['FACTION' => $FACTION]);
		self::notifyPlayer(Factions::getPlayerID($FACTION), $FACTION . 'Deck', '', ['card' => $card]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('next');
	}
	function acContingency(string $FACTION, array $cards): void
	{
//
// Check Contingency
//
		$this->checkAction('play');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
		if (sizeof($cards) !== 1) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		Factions::setStatus($FACTION, 'action', [['name' => 'contingency', 'cards' => $cards]]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', clienttranslate('${FACTION} <B>Contingency</B>'), ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('action');
	}
	function acDeploy(string $FACTION, string $location, string $faction, string $type): void
	{
//
// Check Deploy
//
		$this->checkAction('deploy');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
		if (!array_key_exists($faction, $this->possible)) throw new BgaVisibleSystemException("Invalid faction: $faction");
		if (!array_key_exists($type, $this->possible[$faction])) throw new BgaVisibleSystemException("Invalid type: $type");
		if (!in_array($location, $this->possible[$faction][$type])) throw new BgaVisibleSystemException("Invalid location: $location");
//
		if ($this->gamestate->state()['name'] === 'action') self::discard();
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('placePiece', clienttranslate('${faction} <B>${type}</B> deploys at <B>${location}</B>'), [
			'faction' => $faction, 'type' => $this->PIECES[$type],
			'location' => $this->REGIONS[$location], 'i18n' => ['type', 'location'],
			'piece' => Pieces::get(Pieces::create($FACTION, $faction, $type, $location))]);
//* -------------------------------------------------------------------------------------------------------- */
		if ($this->gamestate->state()['name'] === 'action') self::action();
		else $this->gamestate->nextState('continue');
	}
	function acMove(string $FACTION, string $location, array $pieces): void
	{
//
// Check Move
//
		$this->checkAction('move');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
		if (!$pieces) throw new BgaVisibleSystemException("No pieces selected");
		foreach ($pieces as $id)
		{
			if (!array_key_exists($id, $this->possible)) throw new BgaVisibleSystemException("Invalid piece: $id");
			if (!in_array($location, $this->possible[$id])) throw new BgaVisibleSystemException("Invalid location: $location");
		}
//
		if ($this->gamestate->state()['name'] === 'action') self::discard();
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
			self::notifyAllPlayers('placePiece', clienttranslate('${faction} <B>${type}</B> moves from <B>${old}</B> to <B>${new}</B>'), [
				'faction' => $piece['faction'], 'type' => $this->PIECES[$piece['type']],
				'old' => $this->REGIONS[$old_location], 'new' => $this->REGIONS[$location],
				'i18n' => ['type', 'old', 'new'],
				'piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
		}
//
		if ($this->gamestate->state()['name'] === 'action') self::action();
		else $this->gamestate->nextState('continue');
	}
	function acAttack(string $FACTION, string $location, array $pieces): void
	{
//
// Check Attack
//
		$this->checkAction('attack');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
		if (!$pieces) throw new BgaVisibleSystemException("No pieces selected");
		foreach ($pieces as $id)
		{
			if (!array_key_exists($id, $this->possible)) throw new BgaVisibleSystemException("Invalid piece: $id");
			if (!in_array($location, $this->possible[$id])) throw new BgaVisibleSystemException("Invalid location: $location");
//
			$piece = Pieces::get($id);
			if (!isset($faction)) $faction = $piece['faction'];
			else if ($faction !== $piece['faction']) throw new BgaVisibleSystemException("Invalid faction: $piece[faction]");
		}
//
		if ($this->gamestate->state()['name'] === 'action') self::discard();
//
		Factions::setStatus(Factions::getActive(), 'attack', ['location' => $location, 'faction' => $faction, 'pieces' => $pieces]);
		Factions::setStatus(Factions::getActive(), 'removedPiece');
		Factions::setStatus(Factions::getInactive(), 'removedPiece');
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', clienttranslate('${faction} Attack on <B>${location}</B>'), [
			'faction' => $faction, 'location' => $this->REGIONS[$location], 'i18n' => ['location']]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('attack');
	}
	function acRetreat(string $FACTION, int $location, int $id): void
	{
//
// Check Retreat
//
		$this->checkAction('retreat');
		if (!array_key_exists('retreat', $this->possible)) throw new BgaVisibleSystemException("Invalid possible: " . json_encode($this->possible));
		if (!array_key_exists($id, $this->possible['retreat'])) throw new BgaVisibleSystemException("Invalid piece: $id");
		if (!in_array($location, $this->possible['retreat'][$id])) throw new BgaVisibleSystemException("Invalid location: $location");
//

		die;
	}
	function acRemovePiece(string $FACTION, int $id, bool $exchange = false): void
	{
//
// Check Remove Piece
//
		$this->checkAction('removePiece');
		switch ($this->gamestate->state()['name'])
		{
			case 'attackRoundDefender':
				if ($FACTION !== Factions::getInactive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
				break;
			default:
				if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
				break;
		}
		if (!array_key_exists('pieces', $this->possible)) throw new BgaVisibleSystemException("Invalid possible: " . json_encode($this->possible));
		if (!in_array($id, $this->possible['pieces'])) throw new BgaVisibleSystemException("Invalid piece: $id");
//
		$piece = Pieces::get($id);
		if (!$piece) throw new BgaVisibleSystemException("Invalid piece: $id");
//
		if ($this->gamestate->state()['name'] === 'attackRoundAttacker')
		{
			$removedPiece = Factions::getStatus(Factions::getInactive(), 'removedPiece');
			if ($removedPiece && Pieces::RANK[$piece['type']] < Pieces::RANK[$removedPiece['type']])
			{
				switch (Pieces::RANK[$removedPiece['type']])
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
		Factions::setStatus($FACTION, 'removedPiece', $piece);
		Pieces::destroy($id);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('removePiece', clienttranslate('${faction} <B>${type}</B> is removed at <B>${location}</B>'), [
			'faction' => $piece['faction'], 'type' => $this->PIECES[$piece['type']],
			'location' => $this->REGIONS[$piece['location']], 'i18n' => ['type', 'location'],
			'piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
		if (!$exchange) $this->gamestate->nextState('continue');
		else Factions::setStatus($FACTION, 'factionExchange', $piece['faction']);
	}
	function acReaction(string $FACTION, int $cardID, $pieceID = null, $location = null): void
	{
//
// Check Reaction
//
		$this->checkAction('reaction');
		if (!array_key_exists('reactions', $this->possible)) throw new BgaVisibleSystemException("Invalid possible: " . json_encode($this->possible));
		if (!in_array($cardID, $this->possible['reactions'])) throw new BgaVisibleSystemException("Invalid card: $cardID");
//
		$card = $this->{$FACTION . 'Deck'}->getCard($cardID);
		if (!$card) throw new BgaVisibleSystemException("Invalid attacker card: $cardID");
		$class = "${FACTION}Deck";
//
		$reaction = $class::DECK[$card['type_arg']]['reaction'];
		if ($pieceID)
		{
			if (!in_array($pieceID, $this->possible['pieces'])) throw new BgaVisibleSystemException("Invalid piece: $pieceID");
			if (Pieces::get($pieceID)['faction'] !== $class::DECK[$card['type_arg']]['faction']) throw new BgaVisibleSystemException("Invalid faction");
		}
//
		switch ($this->gamestate->state()['name'])
		{
//
			case 'attackRoundAttacker':
//
				switch ($reaction)
				{
					case 'SustainAttack':
						if (intval(self::getGameStateValue('round')) % 4 === 3) throw new BgaUserException(self::_('You may not use a Sustain Attack reaction during a Winter turn'));
						break;
					case 'AntiAir':
					case 'Naval':
						break;
					default:
						throw new BgaVisibleSystemException("Invalid reaction for attacker: $reaction");
				}
				break;
//
			case 'attackRoundDefender':
//
				switch ($reaction)
				{
					case 'StandFast':
					case 'AntiAir':
					case 'Naval':
					case 'Exchange':
						break;
					case 'Retreat':
						if (!array_key_exists('retreat', $this->possible)) throw new BgaVisibleSystemException("Invalid possible: " . json_encode($this->possible));
						if (!array_key_exists($pieceID, $this->possible['retreat'])) throw new BgaVisibleSystemException("Invalid piece: $pieceID");
						if (!in_array($location, $this->possible['retreat'][$pieceID])) throw new BgaVisibleSystemException("Invalid location: $location");
						break;
					default:
						throw new BgaVisibleSystemException("Invalid reaction for defender: $reaction");
				}
				break;
//
			case 'attackRoundAdvance':
//
				switch ($reaction)
				{
					case 'Advance':
						if (intval(self::getGameStateValue('round')) % 4 === 0) throw new BgaUserException(self::_('You cannot use the Advance!reaction during a Spring turn'));
						break;
					default:
						throw new BgaVisibleSystemException("Invalid reaction for attacker: $reaction");
				}
				break;
//
			default:
//
				throw new BgaVisibleSystemException("Invalid game state for reaction: " . $this->gamestate->state()['name']);
//
		}
//
		$this->{$FACTION . 'Deck'}->playCard($cardID);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers($FACTION . 'Play', clienttranslate('${FACTION} <B>${reaction}</B>${CARD} '), [
			'card' => $card, 'CARD' => ['FACTION' => $FACTION, 'card' => $card], 'FACTION' => $FACTION, 'reaction' => $this->REACTIONS[$reaction], 'i18n' => ['reaction']]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers($FACTION . 'Discard', '', ['card' => $card]);
//* -------------------------------------------------------------------------------------------------------- */
		switch ($reaction)
		{
//
			case 'Exchange':
//
				self::acRemovePiece($FACTION, $pieceID, true);
				$this->gamestate->nextState('exchange');
//
				break;
//
			case 'Retreat':
//
				$piece = Pieces::get($pieceID);
//
				$old_location = $piece['location'];
				$piece['location'] = $location;
				Pieces::setLocation($pieceID, $location);
//* -------------------------------------------------------------------------------------------------------- */
				self::notifyAllPlayers('placePiece', clienttranslate('${faction} <B>${type}</B> retreat from <B>${old}</B> to <B>${new} </B>'), [
					'faction' => $piece['faction'], 'type' => $this->PIECES[$piece['type']],
					'old' => $this->REGIONS[$old_location], 'new' => $this->REGIONS[$location],
					'i18n' => ['type', 'old', 'new'],
					'piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
				$this->gamestate->nextState('retreat');
//
				break;
//
			case 'Advance':
//
				$location = Factions::getStatus($FACTION, 'attack')['location'];

				$piece = Pieces::get($pieceID);
//
				$old_location = $piece['location'];
				$piece ['location'] = $location;
				Pieces::setLocation($pieceID, $location);
//* -------------------------------------------------------------------------------------------------------- */
				self::notifyAllPlayers('placePiece', clienttranslate('${faction} <B>${type}</B> advance from <B>${old}</B> to <B>${new} </B>'), [
					'faction' => $piece['faction'], 'type' => $this->PIECES[$piece['type']],
					'old' => $this->REGIONS[$old_location], 'new' => $this->REGIONS[$location],
					'i18n' => ['type', 'old', 'new'],
					'piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
				$this->gamestate->nextState('end');
//
				break;
//
			default:
//
				$this->gamestate->nextState('continue');
//
		}
	}
}
