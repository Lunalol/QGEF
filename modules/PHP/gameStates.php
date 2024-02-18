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
//
// ➤ Take the deck of cards for the sideyou’re playing, Axis or Allies, and separate the Mid War cards from the Late War cards, and set aside your Late War cards.
// (The Axis-Soviet conflict started in the middle of World War 2)
//
		AlliesDeck::setup($this->alliesDeck);
		AxisDeck::setup($this->axisDeck);
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
		$this->alliesDeck->shuffle('deck');
		$this->axisDeck->shuffle('deck');
//
		if (!self::getGameStateValue('firstGame'))
		{
			$this->alliesDeck->pickCards(7, 'deck', Factions::ALLIES);
			$this->axisDeck->pickCards(7, 'deck', Factions::AXIS);
		}
//
// However, if this is your first game, you may want to select the 7 cards labeled “First Game” instead, and shuffle the rest.
//
		if (self::getGameStateValue('firstGame'))
		{
			$this->alliesDeck->moveCards(array_keys($this->alliesDeck->getCardsOfTypeInLocation(FIRST_GAME, null, 'deck')), 'hand', Factions::ALLIES);
			$this->axisDeck->moveCards(array_keys($this->axisDeck->getCardsOfTypeInLocation(FIRST_GAME, null, 'deck')), 'hand', Factions::AXIS);
		}
//
// At the beginning of the game,
// the Axis control all land spaces west of the 1941 line,
// and the Allies control all land spaces east of that line.
//
		foreach (board::REGIONS as $location => $region)
		{
			if ($region['type'] === LAND) self::DbQuery("INSERT INTO control VALUES ($location, '" . (in_array(1941, $region) ? Factions::AXIS : Factions::ALLIES) . "', 'land')");
			if ($region['type'] === WATER) self::DbQuery("INSERT INTO control VALUES ($location, 'both', 'water')");
		}
//
		Factions::setActivation();
//
		$this->gamestate->nextState('startOfGame');
	}
	function stStartOfRound()
	{
		$round = intval(self::incGameStateValue('round', 1));
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${LOG}${round}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Start of round'), 'round' => $round]);
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
		self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${faction}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Start of turn'), 'faction' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('next');
	}
	function stFirstMovementStep()
	{
		$FACTION = Factions::getActive();
		Factions::updateControl();
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${faction}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('First Movement step'), 'faction' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		foreach (Pieces::getAll($FACTION) as $piece) Pieces::setStatus($piece['id'], 'moved', 'no');
//
		$this->gamestate->nextState('next');
	}
	function stFirstActionStep()
	{
		$FACTION = Factions::getActive();
		Factions::updateControl();
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${faction}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('First Action step'), 'faction' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('next');
	}
	function stSecondActionStep()
	{
		$FACTION = Factions::getActive();
		Factions::updateControl();
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${faction}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Second Action step'), 'faction' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('next');
	}
	function stSecondMovementStep()
	{
		$FACTION = Factions::getActive();
		Factions::updateControl();
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${faction}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Second Movement step'), 'faction' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		foreach (Pieces::getAll($FACTION) as $piece) if ($piece['type'] === 'tank' || $piece['type'] === 'fleet') Pieces::setStatus($piece['id'], 'moved', 'no');
//
		$this->gamestate->nextState('next');
	}
	function stSupplyStep()
	{
		$FACTION = Factions::getActive();
		Factions::updateControl();
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${faction}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Supply step'), 'faction' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('next');
	}
	function stDrawStep()
	{
		$FACTION = Factions::getActive();
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${faction}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('Draw step'), 'faction' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$toDraw = min(3, max(0, 5 - $this->{$FACTION . 'Deck'}->countCardInLocation('hand', $FACTION)));
		for ($i = 0; $i < $toDraw; $i++)
		{
			$card = $this->{$FACTION . 'Deck'}->pickCard('deck', $FACTION);
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers($FACTION . 'Deck', '${faction} Draw 1 card', ['card' => $card, 'faction' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		}
		$this->gamestate->nextState('next');
	}
	function stEndOfFactionRound()
	{
		$FACTION = Factions::getActive();
		Factions::setActivation($FACTION, 'done');
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${faction}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('End of turn'), 'faction' => $FACTION]);
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
		self::notifyAllPlayers('updateRound', '<span class="QGEF-phase">${round}${LOG}</span>', ['i18n' => ['LOG'], 'LOG' => clienttranslate('End of round'), 'round' => $round]);
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
			foreach (array_keys(Factions::FACTIONS) as $FACTION)
			{
				$VP = 0;
				foreach (Factions::getControl($FACTION) as $location) if (array_key_exists('VP', Board::REGIONS[$location])) $VP += Board::REGIONS[$location]['VP'];
				Factions::incVP($FACTION, $VP);
//* -------------------------------------------------------------------------------------------------------- */
				self::notifyAllPlayers('updateVP', '${faction} gains ${VP} VP(s)', ['VP' => $VP, 'faction' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
			}
			if (abs(Factions::getVP('allies') - Factions::getVP('axis')) >= 10) return $this->gamestate->nextState('endOfGame');
		}
//
		Factions::setActivation();
//
		$this->gamestate->nextState('startOfRound');
	}
}
