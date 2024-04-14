<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
trait gameStateActions
{
	function acMulligan(bool $mulligan): void
	{
		$this->checkAction('mulligan');
//
		$player_id = intval(self::getCurrentPlayerId());
//
		if ($mulligan)
		{
			foreach (array_keys(Factions::FACTIONS) as $FACTION)
			{
				if (Factions::getPlayerID($FACTION) === $player_id)
				{
					$deck = $this->{$FACTION . 'Deck'};
					foreach ($deck->getCardsInLocation('hand', $FACTION) as $card)
					{
						self::notifyAllPlayers($FACTION . 'Discard', '', ['card' => ['id' => $card['id']]]);
						$deck->moveCard($card['id'], 'deck');
					}
//
					$this->{$FACTION . 'Deck'}->shuffle('deck');
//
					for ($i = 0; $i < 7; $i++)
					{
						$card = $deck->pickCard('deck', $FACTION);
//* -------------------------------------------------------------------------------------------------------- */
						self::notifyPlayer($player_id, $FACTION . 'Deck', '', ['card' => $card]);
					}
				}
			}
		}
//
		$this->gamestate->setPlayerNonMultiactive($player_id, 'startOfGame');
	}
	function acPass(string $FACTION): void
	{
		$this->checkAction('pass');
//
		if ($this->gamestate->state()['name'] === 'attackRoundSpecial')
		{
			if ($FACTION !== Factions::getInActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
//
			$this->gamestate->nextState('pass');
		}
		else if ($this->gamestate->state()['name'] === 'attackRoundAttacker' || $this->gamestate->state()['name'] === 'attackRoundAdvance')
		{
			if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
//
			$this->gamestate->nextState('endCombat');
			self::action();
		}
		if ($this->gamestate->state()['name'] === 'actionStep')
		{
			if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
//
			self::incGameStateValue('action', 1);
			$this->gamestate->nextState('next');
		}
		else
		{
			if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
//
			Pieces::setStatus(Pieces::ALL, 'moved');
			$this->gamestate->nextState('next');
		}
	}
	function acCancel(string $FACTION): void
	{
		$this->checkAction('cancel');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', clienttranslate('${FACTION} Last action canceled'), ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
//
		$undo = Actions::getLastUndo();
		if ($undo)
		{
			$action = Actions::get($undo);
			switch ($action['name'])
			{
				case 'move':
//
					Pieces::setLocation($action['piece']['id'], $action['piece']['location']);
					Pieces::setStatus($action['piece']['id'], 'moved', 'no');
//* -------------------------------------------------------------------------------------------------------- */
					self::notifyAllPlayers('placePiece', '', ['piece' => $action['piece']]);
//* -------------------------------------------------------------------------------------------------------- */
					break;
//
				case 'deploy':
//
					Pieces::destroy($action['piece']['id']);
//* -------------------------------------------------------------------------------------------------------- */
					self::notifyAllPlayers('removePiece', '', ['piece' => $action['piece']]);
//* -------------------------------------------------------------------------------------------------------- */
					break;
//
				case 'remove':
//* -------------------------------------------------------------------------------------------------------- */
					self::notifyAllPlayers('placePiece', '', ['piece' => Pieces::get(Pieces::create($action['piece']['player'], $action['piece']['faction'], $action['piece']['type'], $action['piece']['location']))]);
//* -------------------------------------------------------------------------------------------------------- */
			}
			Actions::remove($undo);
		}
//
		$action = Actions::getLastAction();
		if ($action) Actions::setStatus($action, 'pending');
		else Actions::clear();
//
		$this->gamestate->nextState('cancel');
	}
	function acPlay(string $FACTION, int $cardID): void
	{
//
// Check Play
//
		$this->checkAction('play');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
//
		$card = $this->{$FACTION . 'Deck'}->getCard($cardID);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '${CARD}', ['CARD' => ['FACTION' => $FACTION, 'card' => $card]]);
//* -------------------------------------------------------------------------------------------------------- */
		foreach (($FACTION . 'Deck')::DECK[$card['type_arg']][$card['type']] as $new)
		{
			$new['cards'] = [$cardID];
			Actions::add('pending', $new);
		}
//
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
		Actions::add('pending', ['name' => 'conscription', 'cards' => $cards]);
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
		Actions::add('pending', ['name' => 'forcedMarch', 'cards' => $cards]);
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
		Actions::add('pending', ['name' => 'desperateAttack', 'cards' => $cards]);
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
		self::incGameStateValue('action', 1);
//
		$this->gamestate->nextState('next');
	}
	function acContingency(string $FACTION, int $cardID): void
	{
//
// Check Contingency
//
		$this->checkAction('play');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
//
		$card = $this->{$FACTION . 'Deck'}->getCard($cardID);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '${CONTINGENCY}', ['CONTINGENCY' => ['FACTION' => $FACTION, 'card' => $card]]);
//* -------------------------------------------------------------------------------------------------------- */
		foreach (($FACTION . 'Deck')::DECK[$card['type_arg']][$card['type']] as $new)
		{
			$new['cards'] = [$cardID];
			Actions::add('pending', $new);
		}
//
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
		$pieceID = Pieces::create($FACTION, $faction, $type, $location);
		$piece = Pieces::get($pieceID);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('placePiece', clienttranslate('${faction} <B>${type}</B> deploys at <B>${location}</B>'), [
			'faction' => $faction, 'type' => $this->PIECES[$type],
			'location' => $this->REGIONS[$location], 'i18n' => ['type', 'location'],
			'piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
		Actions::add('undo', ['name' => 'deploy', 'piece' => $piece]);
//
		self::action();
	}
	function acMove(string $FACTION, string $location, array $pieces): void
	{
//
// Check Move
//
		$this->checkAction('move');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
		if (!$pieces) throw new BgaVisibleSystemException("No pieces selected");
		if (!array_key_exists('move', $this->possible)) throw new BgaVisibleSystemException("Invalid possible: " . json_encode($this->possible));
		foreach ($pieces as $id)
		{
			if (!array_key_exists($id, $this->possible['move'])) throw new BgaVisibleSystemException("Invalid piece: $id");
			if (!in_array($location, $this->possible['move'][$id])) throw new BgaVisibleSystemException("Invalid location: $location");
		}
//
		foreach ($pieces as $id)
		{
			$piece = Pieces::get($id);
//
			if ($piece['location'] !== $location) Pieces::setStatus($id, 'moved');
//
			Actions::add('undo', ['name' => 'move', 'piece' => $piece]);
//
			$old_location = $piece['location'];
			$piece['location'] = $location;
			Pieces::setLocation($id, $piece['location']);
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers('placePiece', clienttranslate('${faction} <B>${type}</B> moves from <B>${old}</B> to <B>${new}</B>'), [
				'faction' => $piece['faction'], 'type' => $this->PIECES[$piece['type']],
				'old' => $this->REGIONS[$old_location], 'new' => $this->REGIONS[$piece['location']],
				'i18n' => ['type', 'old', 'new'],
				'piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
		}
//
		self::action();
	}
	function acAttack(string $FACTION, string $location, array $pieces): void
	{
//
// Check Attack
//
		$this->checkAction('attack');
		if ($FACTION !== Factions::getActive()) throw new BgaVisibleSystemException("Invalid FACTION: $FACTION");
		if (!$pieces) throw new BgaVisibleSystemException("No pieces selected");
		if (!array_key_exists('attack', $this->possible)) throw new BgaVisibleSystemException("Invalid possible: " . json_encode($this->possible));
		foreach ($pieces as $id)
		{
			if (!array_key_exists($id, $this->possible['attack'])) throw new BgaVisibleSystemException("Invalid piece: $id");
			if (!in_array($location, $this->possible['attack'][$id])) throw new BgaVisibleSystemException("Invalid location: $location");
//
			$piece = Pieces::get($id);
			if (!isset($faction)) $faction = $piece['faction'];
			else if ($faction !== $piece['faction']) throw new BgaVisibleSystemException("Invalid faction: $piece[faction]");
		}
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
	function acRemovePiece(string $FACTION, int $id, bool $exchange = false): void
	{
//
// Check Remove Piece
//
		$this->checkAction('removePiece');
//
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
		Pieces::destroy($id);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('removePiece', clienttranslate('${faction} <B>${type}</B> is removed at <B>${location}</B>'), [
			'faction' => $piece['faction'], 'type' => $this->PIECES[$piece['type']],
			'location' => $this->REGIONS[$piece['location']], 'i18n' => ['type', 'location'],
			'piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
		Actions::add('undo', ['name' => 'remove', 'piece' => $piece]);
//
		if ($this->gamestate->state()['name'] === 'attackRoundExchange')
		{
			if (in_array($piece['type'], ['airplane', 'Fleet'])) $this->gamestate->nextState('special');
			else if ($exchange)
			{
				Factions::setStatus($FACTION, 'factionExchange', $piece['faction']);
				$this->gamestate->nextState('exchange');
			}
			else $this->gamestate->nextState('continue');
		}
		else $this->gamestate->nextState('continue');
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
			if (Pieces::get($pieceID)['faction'] !== $class::DECK[$card['type_arg']]['faction']) throw new BgaVisibleSystemException("Invalid FACTION");
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
			case 'attackRoundSpecial':
//
				switch ($reaction)
				{
					case 'AntiAir':
					case 'Naval':
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
				self::notifyAllPlayers('placePiece', clienttranslate('${faction} <B>${type}</B> retreats from <B>${old}</B> to <B>${new} </B>'), [
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
				self::notifyAllPlayers('placePiece', clienttranslate('${faction} <B>${type}</B> advances from <B>${old}</B> to <B>${new} </B>'), [
					'faction' => $piece['faction'], 'type' => $this->PIECES[$piece['type']],
					'old' => $this->REGIONS[$old_location], 'new' => $this->REGIONS[$location],
					'i18n' => ['type', 'old', 'new'],
					'piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
				$this->gamestate->nextState('endCombat');
				self::action();
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
