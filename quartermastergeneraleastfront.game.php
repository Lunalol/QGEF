<?php
//
require_once( APP_GAMEMODULE_PATH . 'module/table/table.game.php' );
require_once('modules/PHP/constants.inc.php');
require_once('modules/PHP/Players.php');
require_once('modules/PHP/Factions.php');
require_once('modules/PHP/AxisDeck.php');
require_once('modules/PHP/AlliesDeck.php');
require_once('modules/PHP/Board.php');
require_once('modules/PHP/Counters.php');
require_once('modules/PHP/Pieces.php');
require_once('modules/PHP/gameStates.php');
require_once('modules/PHP/gameStateArguments.php');
require_once('modules/PHP/gameStateActions.php');
require_once('modules/PHP/gameUtils.php');

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class QuartermasterGeneralEastFront extends Table
{
	use gameStates;
	use gameStateArguments;
	use gameStateActions;
	use gameUtils;

	function __construct()
	{
		parent::__construct();
//
// Initialize Globals
//
		self::initGameStateLabels([
//
// Game options
//
			'duel' => DUEL,
			'factionsChoice' => FACTIONSCHOICE,
			'firstGame' => FIRSTGAME,
//
// Globals
//
			'round' => ROUND, 'action' => ACTION, 'rank' => RANK
		]);
//
// Initialize Decks
//
		$this->alliesDeck = AlliesDeck::init();
		$this->axisDeck = AxisDeck::init();
	}
	protected function getGameName()
	{
		return "quartermastergeneraleastfront";
	}
	protected function setupNewGame($players, $options = [])
	{
		$gameinfos = self::getGameinfos();
		$admin = Players::getAdminPlayerID();
//
		switch (sizeof($players))
		{
//
			case 1:
//
// ONE player game
//
				Factions::create(Factions::ALLIES, $admin);
				Factions::create(Factions::AXIS, $admin);
//
				$players[$admin]['player_color'] = '000000';
//
				break;
//
			case 2:
//
// TWO players game
//
				$IDs = array_keys($players);
//
				switch ($options[FACTIONSCHOICE])
				{
//
					case 0:
//
// Faction is randomly chosen
//
						shuffle($IDs);
//
						break;
//
					case 1:
//
// Table administrator will play Allies
//
						$IDs = array_diff($IDs, [$admin]);
						array_unshift($IDs, $admin);
//
						break;
//
					case 2:
//
// Table administrator will play Axis
//
						$IDs = array_diff($IDs, [$admin]);
						array_push($IDs, $admin);
//
						break;
//
					default: throw new BgaVisibleSystemException('Invalid factionsChoice: ' . $options[FACTIONSCHOICE]);
				}
//
				foreach (array_keys(Factions::FACTIONS) as $FACTION)
				{
					$ID = array_shift($IDs);
					Factions::create($FACTION, $ID);
					$players[$ID]['player_color'] = $gameinfos['player_colors'][$FACTION];
				}
//
				break;
//
			default: throw new BgaVisibleSystemException('Invalid number of players: ' . sizeof($players));
		}
//
		Players::create($players);
//
// Color Preferences
//
		if (sizeof($players) === 1 && false)
		{
			$gameinfos = self::getGameinfos();
			self::reattributeColorsBasedOnPreferences($players, $gameinfos['player_colors']);
			self::reloadPlayersBasicInfos();
		}
//
		$this->activeNextPlayer();
	}
	protected function getAllDatas()
	{
		$player_id = intval(self::getCurrentPlayerId());
//
		$result = [
//
			'REGIONS' => $this->REGIONS,
			'ADJACENCY' => Board::ADJACENCY,
			'PIECES' => Pieces::PIECES,
			'FACTIONS' => Factions::FACTIONS,
			'CARDS' => [Factions::ALLIES => AlliesDeck::DECK, Factions::AXIS => AxisDeck::DECK],
//
			'players' => self::getCollectionFromDb("SELECT player_id id, player_score score FROM player"),
			'factions' => Factions::getAllDatas(),
//
			'contingency' => [
				Factions::ALLIES => $this->alliesDeck->getCardsInLocation('contingency'),
				Factions::AXIS => $this->axisDeck->getCardsInLocation('contingency'),
			],
//
			'decks' => [
				Factions::ALLIES => $this->alliesDeck->countCardInLocation('deck'),
				Factions::AXIS => $this->axisDeck->countCardInLocation('deck'),
			],
			'discards' => [
				Factions::ALLIES => $this->alliesDeck->countCardInLocation('discard'),
				Factions::AXIS => $this->axisDeck->countCardInLocation('discard'),
			],
			'hands' => [
				Factions::ALLIES => $this->alliesDeck->countCardInLocation('hand'),
				Factions::AXIS => $this->axisDeck->countCardInLocation('hand'),
			],
//
			'pieces' => Pieces::getAllDatas(),
			'counters' => Counters::getAllDatas(),
			'private' => [],
//
		];
//
		if ($player_id === Factions::getPlayerID(Factions::ALLIES)) $result['private'][Factions::ALLIES] = $this->alliesDeck->getPlayerHand(Factions::ALLIES);
		if ($player_id === Factions::getPlayerID(Factions::AXIS)) $result['private'][Factions::AXIS] = $this->axisDeck->getPlayerHand(Factions::AXIS);
//
		return $result;
	}
	function dbGetScore(int $player_id): int
	{
		return intval(self::getUniqueValueFromDB("SELECT player_score FROM player WHERE player_id = $player_id"));
	}
	function dbSetScore(int $player_id, int $score, int $score_aux = 0): void
	{
		self::DbQuery("UPDATE player SET player_score = $score, player_score_aux = $score_aux WHERE player_id = $player_id");
		self::notifyAllPlayers('updateScore', '', ['player_id' => $player_id, 'score' => $score]);
	}
	function dbIncScore(int $player_id, int $inc): void
	{
		$this->dbSetScore($player_id, self::dbGetScore($player_id + $inc));
	}
	function getGameProgression()
	{
		return self::getGameStateValue('round') * 10;
	}
	function zombieTurn($state, $active_player)
	{
		$statename = $state['name'];
		if ($state['type'] === "activeplayer")
		{
			switch ($statename)
			{
				default: return $this->gamestate->nextState("zombiePass");
			}
			return;
		}
		if ($state['type'] === "multipleactiveplayer") return $this->gamestate->setPlayerNonMultiactive($active_player, '');
//
		throw new feException("Zombie mode not supported at this game state: " . $statename);
	}
//
// Debug functions
//
	function ckeckADJACENCY()
	{
		foreach ($this->ADJACENCY as $from => $regions)
		{
			foreach ($regions as $to)
			{
				if (!in_array($from, $this->ADJACENCY[$to])) var_dump($from, $to);
				if (!in_array($to, $this->ADJACENCY[$from])) var_dump($to, $from);
			}
		}
	}
	function X()
	{

	}
//
// Debug functions
//
}
