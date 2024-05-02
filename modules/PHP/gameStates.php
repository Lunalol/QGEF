<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
trait gameStates
{
	function stGameSetup()
	{
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '<span class="QGEF-phase">${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Setup')]);
//* -------------------------------------------------------------------------------------------------------- */
//
// ➤ Place both Victory Point markers on space “0” of the Victory Point track,
// and the Game Round marker on the gray space at the top of the Game Round track,
// pointing to the “Summer 1941” space.
//
		Markers::create('allies', 0);
		Markers::create('axis', 0);
//
// ➤ Take the deck of cards for the sideyou’re playing, Axis or Allies, and separate the Mid War cards from the Late War cards, and set aside your Late War cards.
// (The Axis-Soviet conflict started in the middle of World War 2)
//
		Decks::setupAllies($this->decks);
		Decks::setupAxis($this->decks);
//
// ➤ Place your starting pieces on the board (see next page).
// ➤ Set the rest of your pieces to the side — these are your available pieces.
//
		foreach (Pieces::STARTING as $FACTION => $FACTIONs) foreach ($FACTIONs as $faction => $TYPEs) foreach ($TYPEs as $type => $locations) foreach ($locations as $location)
//* -------------------------------------------------------------------------------------------------------- */
						self::notifyAllPlayers('placePiece', '', ['piece' => Pieces::get(Pieces::create($FACTION, $faction, $type, $location))]);
//* -------------------------------------------------------------------------------------------------------- */
//
// ➤ Shuffle your Mid War cards and draw 7 cards.
//
		$this->decks->shuffle(Factions::ALLIES);
		$this->decks->shuffle(Factions::AXIS);
//
		if (!self::getGameStateValue('firstGame'))
		{
			$this->decks->pickCards(7, Factions::ALLIES, Factions::ALLIES);
			$this->decks->pickCards(7, Factions::AXIS, Factions::AXIS);
		}
//
// However, if this is your first game, you may want to select the 7 cards labeled “First Game” instead, and shuffle the rest.
//
		if (self::getGameStateValue('firstGame'))
		{
			$this->decks->moveCards(array_keys($this->decks->getCardsOfTypeInLocation(Decks::FIRST_GAME, null, Factions::ALLIES)), 'hand', Factions::ALLIES);
			$this->decks->moveCards(array_keys($this->decks->getCardsOfTypeInLocation(Decks::MID, null, Factions::ALLIES)), 'hand', Factions::ALLIES);
			$this->decks->moveCards(array_keys($this->decks->getCardsOfTypeInLocation(Decks::FIRST_GAME, null, Factions::AXIS)), 'hand', Factions::AXIS);
			$this->decks->moveCards(array_keys($this->decks->getCardsOfTypeInLocation(Decks::MID, null, Factions::AXIS)), 'hand', Factions::AXIS);
		}
//
// At the beginning of the game,
// the Axis control all land spaces west of the 1941 line,
// and the Allies control all land spaces east of that line.
//
		foreach (Board::REGIONS as $location => $regions) if ($regions['type'] === WATER) self::DbQuery("INSERT INTO control VALUES ($location, 'both','both', 'water')");
		foreach (Board::W1941 as $location) if (Board::REGIONS[$location]['type'] === LAND) self::DbQuery("INSERT INTO control VALUES ($location, '" . Factions::AXIS . "', '" . Factions::AXIS . "', 'land')");
		foreach (Board::E1941 as $location) if (Board::REGIONS[$location]['type'] === LAND) self::DbQuery("INSERT INTO control VALUES ($location, '" . Factions::ALLIES . "', '" . Factions::ALLIES . "', 'land')");
//
// If the score is tied, the Axis player wins
//
		self::dbSetScore(Factions::getPlayerID(Factions::AXIS), 0, 1);
//
		Factions::setActivation();
//
		if (self::getPlayersNumber() === 2 && !self::getGameStateValue('firstGame'))
		{
			$this->gamestate->setAllPlayersMultiactive();
			$this->gamestate->nextState('mulligan');
		}
		else $this->gamestate->nextState('startOfGame');
	}
	function stStartOfRound()
	{
		$round = intval(self::incGameStateValue('round', 1));
		$steps = [1 => 0, 2 => 1, 3 => 2, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 10, 9 => 11, 10 => 12, 11 => 13, 12 => 15, 13 => 16, 14 => 17, 15 => 18, 16 => 19][$round];
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${LOG}${round}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Start of round'), 'round' => $round, 'steps' => $steps]);
//* -------------------------------------------------------------------------------------------------------- */
		Factions::setActivation(Factions::AXIS, 'yes');
//
		$this->gamestate->nextState('startOfFactionRound');
	}
	function stStartOfFactionRound()
	{
		$FACTION = Factions::getActive();
		$this->gamestate->changeActivePlayer(Factions::getPlayerID($FACTION));
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '<span class="QGEF-phase">${FACTION}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Start of turn'), 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		self::setGameStateValue('action', 1);
//
		$this->gamestate->nextState('next');
	}
	function stFirstMovementStep()
	{
		$FACTION = Factions::getActive();
		Board::updateControl(true);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateControl', '', [Factions::ALLIES => Board::getControl(Factions::ALLIES), Factions::AXIS => Board::getControl(Factions::AXIS)]);
		self::notifyAllPlayers('updateSupply', '', [Factions::ALLIES => Board::getSupplyLines(Factions::ALLIES), Factions::AXIS => Board::getSupplyLines(Factions::AXIS)]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '<span class="QGEF-phase">${FACTION}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('First Movement step'), 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		foreach (Pieces::getAll($FACTION) as $piece) Pieces::setStatus($piece['id'], 'moved', 'no');
		Actions::clear();
//
		$this->gamestate->nextState('firstMovementStep');
	}
	function stActionStep()
	{
		$action = intval(self::getGameStateValue('action'));
		if ($action > 2) return $this->gamestate->nextState('next');
//
		$FACTION = Factions::getActive();
		$this->gamestate->changeActivePlayer(Factions::getPlayerID($FACTION));
//
		Board::updateControl(true);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateControl', '', [Factions::ALLIES => Board::getControl(Factions::ALLIES), Factions::AXIS => Board::getControl(Factions::AXIS)]);
		self::notifyAllPlayers('updateSupply', '', [Factions::ALLIES => Board::getSupplyLines(Factions::ALLIES), Factions::AXIS => Board::getSupplyLines(Factions::AXIS)]);
//* -------------------------------------------------------------------------------------------------------- */
//* -------------------------------------------------------------------------------------------------------- */
		if ($action === 1) self::notifyAllPlayers('msg', '<span class="QGEF-phase">${FACTION}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('First Action step'), 'FACTION' => $FACTION]);
		if ($action === 2) self::notifyAllPlayers('msg', '<span class="QGEF-phase">${FACTION}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Second Action step'), 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		Actions::clear();
//
		$this->gamestate->nextState('actionStep');
	}
	function stSecondMovementStep()
	{
		$FACTION = Factions::getActive();
		Board::updateControl(true);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateControl', '', [Factions::ALLIES => Board::getControl(Factions::ALLIES), Factions::AXIS => Board::getControl(Factions::AXIS)]);
		self::notifyAllPlayers('updateSupply', '', [Factions::ALLIES => Board::getSupplyLines(Factions::ALLIES), Factions::AXIS => Board::getSupplyLines(Factions::AXIS)]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '<span class="QGEF-phase">${FACTION}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Second Movement step'), 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		foreach (Pieces::getAll($FACTION) as $piece) if ($piece['type'] === 'tank' || $piece['type'] === 'fleet') Pieces::setStatus($piece['id'], 'moved', 'no');
		Actions::clear();
//
		$this->gamestate->nextState('next');
	}
	function stSupplyStep()
	{
		$FACTION = Factions::getActive();
		Board::updateControl(true);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '<span class="QGEF-phase">${FACTION}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Supply step'), 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$supplyLines = Board::getSupplyLines($FACTION);
		foreach (array_filter(Pieces::getAll($FACTION), fn($piece) => !in_array($piece['location'], $supplyLines[$piece['faction']])) as $piece)
		{
			Pieces::destroy($piece['id']);
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers('removePiece', clienttranslate('${faction} <B>${type}</B> is removed at <B>${location}</B>'), [
				'faction' => $piece['faction'], 'type' => $this->PIECES[$piece['type']],
				'location' => $this->REGIONS[$piece['location']], 'i18n' => ['type', 'location'],
				'piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
		}
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateControl', '', [Factions::ALLIES => Board::getControl(Factions::ALLIES), Factions::AXIS => Board::getControl(Factions::AXIS)]);
		self::notifyAllPlayers('updateSupply', '', [Factions::ALLIES => Board::getSupplyLines(Factions::ALLIES), Factions::AXIS => Board::getSupplyLines(Factions::AXIS)]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('next');
	}
	function stDrawStep()
	{
		$FACTION = Factions::getActive();
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '<span class="QGEF-phase">${FACTION}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Draw step'), 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$toDraw = min(3, max(0, 5 - $this->decks->countCardInLocation('hand', $FACTION)));
		for ($i = 0; $i < $toDraw; $i++)
		{
			$card = $this->decks->pickCard($FACTION, $FACTION);
			if (!$card)
			{
//* -------------------------------------------------------------------------------------------------------- */
				self::notifyPlayer(Factions::getPlayerID($FACTION), 'msg', _('Your deck is empty'), []);
//* -------------------------------------------------------------------------------------------------------- */
				break;
			}
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers('msg', '${FACTION} Draw 1 card', ['FACTION' => $FACTION]);
			self::notifyPlayer(Factions::getPlayerID($FACTION), $FACTION . 'Deck', '', ['card' => $card]);
//* -------------------------------------------------------------------------------------------------------- */
		}
		$this->gamestate->nextState('next');
	}
	function stEndOfFactionRound()
	{
		$FACTION = Factions::getActive();
		Factions::setActivation($FACTION, 'done');
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '<span class="QGEF-phase">${FACTION}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('End of turn'), 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		switch ($FACTION)
		{
			case 'axis':
				Factions::setActivation(Factions::ALLIES, 'yes');
				return $this->gamestate->nextState('startOfFactionRound');
			case 'allies':
				return $this->gamestate->nextState('endOfRound');
		}
//
	}
	function stEndOfRound()
	{
		$round = intval(self::getGameStateValue('round'));
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '<span class="QGEF-phase">${round}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('End of round'), 'round' => $round]);
//* -------------------------------------------------------------------------------------------------------- */
//
// + Late Cards
//
// TODO
//
// Scoring
//
		if (in_array($round, [3, 7, 11, 16]))
		{
			$steps = [3 => 3, 7 => 9, 11 => 14, 16 => 20][$round];
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Scoring'), 'steps' => $steps]);
//* -------------------------------------------------------------------------------------------------------- */

			foreach (array_keys(Factions::FACTIONS) as $FACTION)
			{
				$VP = 0;
				foreach (Board::getControl($FACTION) as $location) if (array_key_exists('VP', Board::REGIONS[$location])) $VP += Board::REGIONS[$location]['VP'];
//
				for ($i = 0; $i < $VP; $i++)
				{
					Markers::setLocation($FACTION, Factions::incVP($FACTION, 1));
//* -------------------------------------------------------------------------------------------------------- */
					self::notifyAllPlayers('placeMarker', '', ['marker' => Markers::get($FACTION)]);
					if (self::getPlayersNumber() === 2) self::notifyAllPlayers('updateScore', '', ['player_id' => Factions::getPlayerID($FACTION), 'VP' => Factions::getVP($FACTION)]);
//* -------------------------------------------------------------------------------------------------------- */
				}
//* -------------------------------------------------------------------------------------------------------- */
				self::notifyAllPlayers('msg', clienttranslate('${FACTION} Gains ${VP} VP(s)'), ['VP' => $VP, 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
			}
			if (abs(Factions::getVP('allies') - Factions::getVP('axis')) >= 10) return $this->gamestate->nextState('endOfGame');
		}
//
		Factions::setActivation();
//
		$this->gamestate->nextState('startOfRound');
	}
	function stAttackRound()
	{
		$this->gamestate->nextState('attackRoundDefender');
	}
	function stAttackRoundDefender()
	{
		$args = self::argAttackRoundDefender();
		if (sizeof($args['defender']) === 0)
		{
			$this->gamestate->changeActivePlayer(Factions::getPlayerID(Factions::getActive()));
			$this->gamestate->nextState('advance');
		}
		else
		{
			$this->gamestate->changeActivePlayer(Factions::getPlayerID(Factions::getInactive()));
			$this->gamestate->nextState('continue');
		}
	}
	function stAttackRoundAttacker()
	{
		$args = self::argAttackRoundAttacker();
		if (sizeof($args['attacker']) === 0)
		{
			$this->gamestate->changeActivePlayer(Factions::getPlayerID(Factions::getActive()));
			$this->gamestate->nextState('endCombat');
		}
		else if (sizeof($args['defender']) === 0)
		{
			$this->gamestate->changeActivePlayer(Factions::getPlayerID(Factions::getActive()));
			$this->gamestate->nextState('advance');
		}
		else
		{
			$this->gamestate->changeActivePlayer(Factions::getPlayerID(Factions::getActive()));
			$this->gamestate->nextState('continue');
		}
	}
	function stAttackRoundExchange()
	{
		$this->gamestate->changeActivePlayer(Factions::getPlayerID(Factions::getActive()));
		$this->gamestate->nextState('continue');
	}
	function stAttackRoundSpecial()
	{
		$this->gamestate->changeActivePlayer(Factions::getPlayerID(Factions::getInActive()));
		$this->gamestate->nextState('continue');
	}
	function stAction()
	{
		$FACTION = Factions::getActive();
		$this->gamestate->changeActivePlayer(Factions::getPlayerID($FACTION));
//
		$id = Actions::getNextAction();
		if ($id)
		{
			$action = Actions::get($id);
//
			switch ($action['name'])
			{
//
				case 'conscription':
				case 'forcedMarch':
				case 'desperateAttack':
				case 'deploy':
				case 'move/attack':
				case 'move':
				case 'attack':
				case 'eliminate':
				case 'discard':
//
					$this->gamestate->nextState('continue');
//
					break;
//
				case 'eliminateVS':
//
					$this->gamestate->nextState('interrupt');
//
					break;
//
				case 'action':
//
					self::incGameStateValue('action', -1);
					self::action();
//
					break;
//
				case 'VP':
//
					$otherFACTION = Factions::getInActive();
//
					$VP = 1;
					Markers::setLocation($otherFACTION, Factions::incVP($otherFACTION, $VP));
//* -------------------------------------------------------------------------------------------------------- */
					self::notifyAllPlayers('placeMarker', '', ['marker' => Markers::get($otherFACTION)]);
//* -------------------------------------------------------------------------------------------------------- */
					if (self::getPlayersNumber() === 2) self::notifyAllPlayers('updateScore', '', ['player_id' => Factions::getPlayerID($otherFACTION), 'VP' => Factions::getVP($otherFACTION)]);
//* -------------------------------------------------------------------------------------------------------- */
					self::notifyAllPlayers('msg', clienttranslate('${FACTION} Gains ${VP} VP(s)'), ['VP' => $VP, 'FACTION' => $otherFACTION]);
//* -------------------------------------------------------------------------------------------------------- */
					self::action();
//
					break;
//
				case 'draw':
//
					for ($i = 0; $i < $action['count']; $i++)
					{
						$card = $this->decks->pickCard($FACTION, $FACTION);
						if (!$card)
						{
//* -------------------------------------------------------------------------------------------------------- */
							self::notifyPlayer(Factions::getPlayerID($FACTION), 'msg', _('Your deck is empty'), []);
//* -------------------------------------------------------------------------------------------------------- */
							break;
						}
//* -------------------------------------------------------------------------------------------------------- */
						self::notifyAllPlayers('msg', '${FACTION} Draw 1 card', ['FACTION' => $FACTION]);
						self::notifyPlayer(Factions::getPlayerID($FACTION), $FACTION . 'Deck', '', ['card' => $card]);
//* -------------------------------------------------------------------------------------------------------- */
					}
//
					self::action();
//
					break;
//
				default:
//
					throw new BgaVisibleSystemException("Invalid action: $action[name]");
			}
		}
		else $this->gamestate->nextState('next');
	}
	function stInterrupt()
	{
		$FACTION = Factions::getInActive();
		$this->gamestate->changeActivePlayer(Factions::getPlayerID($FACTION));
//
		if (!self::argAction()['eliminate'])
		{
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyPlayer(Factions::getPlayerID(Factions::getActive()), 'msg', _('Nothing to do'), []);
			return self::action();
//* -------------------------------------------------------------------------------------------------------- */
		}
//
		$this->gamestate->nextState('continue');
	}
}
