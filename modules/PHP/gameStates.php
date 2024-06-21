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
		if (self::getGameStateValue('firstGame') == 1)
		{
			$this->decks->moveCards(array_keys($this->decks->getCardsOfTypeInLocation(Decks::FIRST_GAME, null, Factions::ALLIES)), 'hand', Factions::ALLIES);
			$this->decks->moveCards(array_keys($this->decks->getCardsOfTypeInLocation(Decks::FIRST_GAME, null, Factions::AXIS)), 'hand', Factions::AXIS);
		}
		if (self::getGameStateValue('firstGame') == 2)
		{
			$this->decks->moveCards(array_keys($this->decks->getCardsOfTypeInLocation(Decks::MID, null, Factions::ALLIES)), 'hand', Factions::ALLIES);
			$this->decks->moveCards(array_keys($this->decks->getCardsOfTypeInLocation(Decks::MID, null, Factions::AXIS)), 'hand', Factions::AXIS);
		}
		if (self::getGameStateValue('firstGame') >= 3)
		{
			$this->decks->moveCards(array_keys($this->decks->getCardsOfTypeInLocation(Decks::LATE, null, 'aside', Factions::ALLIES)), 'hand', Factions::ALLIES);
			$this->decks->moveCards(array_keys($this->decks->getCardsOfTypeInLocation(Decks::LATE, null, 'aside', Factions::AXIS)), 'hand', Factions::AXIS);
		}
//
// At the beginning of the game,
// the Axis control all land spaces west of the 1941 line,
// and the Allies control all land spaces east of that line.
//
		foreach (Board::REGIONS as $location => $regions) if ($regions['type'] === WATER) self::DbQuery("INSERT INTO control VALUES ($location, 'both','both','both', 'water')");
		foreach (Board::W1941 as $location) if (Board::REGIONS[$location]['type'] === LAND) self::DbQuery("INSERT INTO control VALUES ($location, '" . Factions::AXIS . "', '" . Factions::AXIS . "', '" . Factions::AXIS . "', 'land')");
		foreach (Board::E1941 as $location) if (Board::REGIONS[$location]['type'] === LAND) self::DbQuery("INSERT INTO control VALUES ($location, '" . Factions::ALLIES . "', '" . Factions::ALLIES . "', '" . Factions::ALLIES . "', 'land')");
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
	function stStartOfTurn()
	{
		$FACTION = Factions::getActive();
		$this->gamestate->changeActivePlayer(Factions::getPlayerID($FACTION));
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '<span class="QGEF-phase">${FACTION}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Start of turn'), 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		if (Factions::getStatus($FACTION, 'mud'))
		{
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers('msg', clienttranslate('<B>General Mud</B>: the Axis cannot use Advance! reactions and must skip the Second Movement step'), []);
//* -------------------------------------------------------------------------------------------------------- */
		}
		self::setGameStateValue('action', 1);
		Board::updateControl('startOfTurn');
//
		$this->gamestate->nextState('next');
	}
	function stFirstMovementStep()
	{
		$FACTION = Factions::getActive();
		Board::updateControl('startOfStep');
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
		Board::updateControl('startOfStep');
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
		Board::updateControl('startOfStep');
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateControl', '', [Factions::ALLIES => Board::getControl(Factions::ALLIES), Factions::AXIS => Board::getControl(Factions::AXIS)]);
		self::notifyAllPlayers('updateSupply', '', [Factions::ALLIES => Board::getSupplyLines(Factions::ALLIES), Factions::AXIS => Board::getSupplyLines(Factions::AXIS)]);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '<span class="QGEF-phase">${FACTION}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Second Movement step'), 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		foreach (Pieces::getAll($FACTION) as $piece) if ($piece['type'] === 'tank' || $piece['type'] === 'fleet') Pieces::setStatus($piece['id'], 'moved', 'no');
		Actions::clear();
//
		if (Factions::getStatus($FACTION, 'mud'))
		{
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers('msg', clienttranslate('<B>General Mud</B>: the Axis must skip the Second Movement step'), []);
//* -------------------------------------------------------------------------------------------------------- */
			Factions::setStatus($FACTION, 'mud');
			$this->gamestate->nextState('next');
		}
		$this->gamestate->nextState('next');
	}
	function stSupplyStep()
	{
		$FACTION = Factions::getActive();
		Board::updateControl('startOfStep');
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('msg', '<span class="QGEF-phase">${FACTION}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Supply step'), 'FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$supplyLines = Board::getSupplyLines($FACTION);
		foreach (Pieces::getAll($FACTION) as $piece)
		{
			if (!in_array($piece['location'], $supplyLines[$piece['faction']]) && !Pieces::getStatus($piece['id'], 'supplied'))
			{
				Pieces::destroy($piece['id']);
//* -------------------------------------------------------------------------------------------------------- */
				self::notifyAllPlayers('removePiece', clienttranslate('${faction} <B>${type}</B> is removed at <B>${location}</B>'), [
					'faction' => $piece['faction'], 'type' => $this->PIECES[$piece['type']],
					'location' => $this->REGIONS[$piece['location']], 'i18n' => ['type', 'location'],
					'piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
			}
			Pieces::setStatus($piece['id'], 'supplied');
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
		for ($i = 0;
			$i < $toDraw;
			$i++)
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
// Scoring
//
		if (in_array($round, [3, 7, 11, 16]))
		{
			$steps = [3 => 3, 7 => 9, 11 => 14, 16 => 20][$round];
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Scoring'), 'steps' => $steps]);
//* -------------------------------------------------------------------------------------------------------- */
			$victoryStars = Board::victoryStars();
			foreach (array_keys(Factions::FACTIONS) as $FACTION)
			{
				$VP = 0;
				foreach (Board::getControl($FACTION) as $location) if (array_key_exists($location, $victoryStars)) $VP += $victoryStars[$location];
//
				for ($i = 0;
					$i < $VP;
					$i++)
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
// + Late Cards
//
		if ($round === 3)
		{
			$steps = [3 => 4][$round];
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Late cards'), 'steps' => $steps]);
//* -------------------------------------------------------------------------------------------------------- */
			$this->decks->moveCards(array_keys($this->decks->getCardsOfTypeInLocation(Decks::LATE, null, 'aside', Factions::ALLIES)), Factions::ALLIES);
			$this->decks->moveCards(array_keys($this->decks->getCardsOfTypeInLocation(Decks::LATE, null, 'aside', Factions::AXIS)), Factions::AXIS);
//
			$this->decks->shuffle(Factions::ALLIES);
			$this->decks->shuffle(Factions::AXIS);
		}
//
		Factions::setActivation();
//
		$this->gamestate->nextState('startOfRound');
	}
	function stAttackRound()
	{
//
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
				case 'discard':
//
					if (!($this->decks->countCardInLocation('hand', $FACTION) > 0)) throw new BgaUserException(self::_('No card to discard'));
					$this->gamestate->nextState('continue');
//
					break;
//
				case 'move/attack':
//
					if (array_key_exists('requirement', $action) && $action['requirement'] === 'noSpringTurn' && intval(self::getGameStateValue('round')) % 4 === 0) self::action();
					else $this->gamestate->nextState('continue');
//
					break;
//
				case 'attack':
//
					if (array_key_exists('requirement', $action) && $action['requirement'] === 'noWinterTurn' && intval(self::getGameStateValue('round')) % 4 === 3) self::action();
					else $this->gamestate->nextState('continue');
//
					break;
//
				case 'conscription':
				case 'forcedMarch':
				case 'desperateAttack':
				case 'deploy':
				case 'recruit':
				case 'move':
				case 'attack':
				case 'eliminate':
				case 'discard':
				case 'scorched':
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
				case 'supply':
//
					$playedPieces = Actions::getPlayedPieces();
					foreach ($playedPieces as $piece) Pieces::setStatus($piece['id'], 'supplied', true);
					self::action();
//
					break;
//
				case 'mud':
//
					Factions::setStatus(Factions::getInactive(), 'mud', true);
					self::action();
//
					break;
//
				case 'Gorki':
//
					Markers::create('Gorki', GORKI);
//* -------------------------------------------------------------------------------------------------------- */
					self::notifyAllPlayers('placeMarker', '', ['marker' => Markers::get('Gorki')]);
//* -------------------------------------------------------------------------------------------------------- */
					self::action();
//
					break;
//
				case 'VP':
//
					$otherFACTION = $action['FACTION'];
//
					if (array_key_exists('special', $action))
					{
						switch ($action['special'])
						{
							case 66: // Gain 1 VP for every 3 Pact pieces in land spaces east of the 1941 line, rounded up
//
								$pieces = 0;
								foreach (Board::E1941 as $location) $pieces += sizeof(Pieces::getAtLocation($location, Factions::PACT));
								$VP = intdiv($pieces + 2, 3);
//
								break;
//
							case 69: // Gain 2 VPs if the Axis controls every land space between the 1939 and 1941 lines
//
								$VP = 2;
								$control = Board::getControl($FACTION);
								foreach (array_intersect(Board::E1941, Board::W1939) as $location) if (!in_array($location, $control)) $VP = 0;
//
								break;
//
							case 84: // gain 1 VP if a Soviet piece is in or adjacent to Warsaw; gain 1 VP if a Soviet piece is in or adjacent to Berlin
//
								$VP = 0;
								foreach ([WARSAW, HUNGARY, LWOW, BREST, EASTPRUSSIA, WESTBALTICSEA, BERLIN] as $location)
								{
									if (Pieces::getAtLocation($location, Factions::SOVIETUNION))
									{
										$VP++;
										break;
									}
								}
								foreach ([BERLIN, VIENNA, HUNGARY, WARSAW, WESTBALTICSEA] as $location)
								{
									if (Pieces::getAtLocation($location, Factions::SOVIETUNION))
									{
										$VP++;
										break;
									}
								}
//
								break;
//
							case 88: // If 2 or more Soviet pieces are in or adjacent to Romania: Gain 1 VP
//
								$pieces = 0;
								foreach ([ROMANIA, BULGARIA, YUGOSLAVIA, HUNGARY, LWOW, BESSARABIA, BLACKSEA, BOSPORUS] as $location) $pieces += sizeof(Pieces::getAtLocation($location, Factions::SOVIETUNION));
								$VP = $pieces >= 2 ? 1 : 0;
//
								break;
//
							default:
//
								throw new BgaVisibleSystemException("Invalid special: $action[special]");
						}
					}
					else if (array_key_exists('locations', $action))
					{
						$VP = 0;
						$control = Board::getControl($FACTION);
						foreach ($action['locations'] as $location) if (in_array($location, $control)) $VP++;
					}
					else $VP = 1;
//
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
					for ($i = 0;
						$i < $action['count'];
						$i++)
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
		$args = self::argAction();
		if (!$args['eliminate'] && !$args['discard'])
		{
			if (array_key_exists('mandatory', $args['action'])) throw new BgaUserException(self::_('No piece to eliminate'));
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers('msg', clienttranslate('No piece to eliminate'), ['FACTION' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
			self::action();
		}
		else $this->gamestate->nextState('continue');
	}
}
