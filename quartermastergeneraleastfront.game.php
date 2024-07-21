<?php
/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
require_once( APP_GAMEMODULE_PATH . 'module/table/table.game.php' );
require_once('modules/PHP/constants.inc.php');
require_once('modules/PHP/Players.php');
require_once('modules/PHP/Factions.php');
require_once('modules/PHP/Decks.php');
require_once('modules/PHP/Board.php');
require_once('modules/PHP/Markers.php');
require_once('modules/PHP/Pieces.php');
require_once('modules/PHP/Actions.php');
require_once('modules/PHP/gameStates.php');
require_once('modules/PHP/gameStateArguments.php');
require_once('modules/PHP/gameStateActions.php');
require_once('modules/PHP/gameUtils.php');

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
			'round' => ROUND, 'action' => ACTION, 'location' => LOCATION
		]);
//
// Initialize Decks
//
		$this->decks = $this->getNew("module.common.deck");
		$this->decks->init("decks");
//
		Players::$table = $this;
		Factions::$table = $this;
		Decks::$table = $this;
		Board::$table = $this;
		Markers::$table = $this;
		Pieces::$table = $this;
		Actions::$table = $this;
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
		self::initStat('player', 'contingency', 0);
		self::initStat('player', 'play', 0);
		self::initStat('player', 'conscription', 0);
		self::initStat('player', 'forcedMarch', 0);
		self::initStat('player', 'desperateAttack', 0);
		self::initStat('player', 'productionInitiative', 0);
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
			'CARDS' => Decks::DECKS,
//
			'players' => self::getCollectionFromDb("SELECT player_id id, player_score score FROM player"),
			'factions' => Factions::getAllDatas(),
			'steps' => [0 => 0, 1 => 0, 2 => 1, 3 => 2, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 10, 9 => 11, 10 => 12, 11 => 13, 12 => 15, 13 => 16, 14 => 17, 15 => 18, 16 => 19][intval(self::getGameStateValue('round'))],
//
			'contingency' => [
				Factions::ALLIES => $this->decks->getCardsInLocation('contingency', Factions::ALLIES),
				Factions::AXIS => $this->decks->getCardsInLocation('contingency', Factions::AXIS),
			],
//
			'decks' => [
				Factions::ALLIES => $this->decks->countCardInLocation(Factions::ALLIES),
				Factions::AXIS => $this->decks->countCardInLocation(Factions::AXIS),
			],
			'discards' => [
				Factions::ALLIES => $this->decks->countCardInLocation('discard', Factions::ALLIES),
				Factions::AXIS => $this->decks->countCardInLocation('discard', Factions::AXIS),
			],
			'hands' => [
				Factions::ALLIES => $this->decks->countCardInLocation('hand', Factions::ALLIES),
				Factions::AXIS => $this->decks->countCardInLocation('hand', Factions::AXIS),
			],
//
			'pieces' => Pieces::getAllDatas(),
			'markers' => Markers::getAllDatas(),
//
			'private' => [],
//
		];
//
		if ($player_id === Factions::getPlayerID(Factions::ALLIES)) $result['private'][Factions::ALLIES] = $this->decks->getPlayerHand(Factions::ALLIES);
		if ($player_id === Factions::getPlayerID(Factions::AXIS)) $result['private'][Factions::AXIS] = $this->decks->getPlayerHand(Factions::AXIS);
//
		$result['factions'][Factions::ALLIES]['control'] = Board::getControl(Factions::ALLIES);
		$result['factions'][Factions::AXIS]['control'] = Board::getControl(Factions::AXIS);
//
		$result['factions'][Factions::ALLIES]['supply'] = Board::getSupplyLines(Factions::ALLIES);
		$result['factions'][Factions::AXIS]['supply'] = Board::getSupplyLines(Factions::AXIS);
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
	}
	function dbIncScore(int $player_id, int $inc): void
	{
		$this->dbSetScore($player_id, self::dbGetScore($player_id + $inc));
	}
	function getGameProgression()
	{
		return round(self::getGameStateValue('round') * 100 / 16);
	}
	function zombieTurn($state, $active_player)
	{
		$statename = $state['name'];
		if ($state['type'] === "activeplayer")
		{
			switch ($statename)
			{
				case 'actionStep':
					self::incGameStateValue('action', 1);
					return $this->gamestate->nextState("zombiePass");
				default:
					return $this->gamestate->nextState("zombiePass");
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
	public function loadBugReportSQL(int $reportId, array $studioPlayersIds): void
	{
		$players = $this->getObjectListFromDb('SELECT player_id FROM player', true);

		// Change for your game
		// We are setting the current state to match the start of a player's turn if it's already game over
		$sql = ['UPDATE global SET global_value = 100 WHERE global_id = 1 AND global_value = 99'];
		foreach ($players as $index => $pId)
		{
			$studioPlayer = $studioPlayersIds[$index];

			// All games can keep this SQL
			$sql[] = "UPDATE player SET player_id=$studioPlayer WHERE player_id=$pId";
			$sql[] = "UPDATE global SET global_value=$studioPlayer WHERE global_value=$pId";
			$sql[] = "UPDATE stats SET stats_player_id=$studioPlayer WHERE stats_player_id=$pId";
			// Add game-specific SQL update the tables for your game
			$sql[] = "UPDATE factions SET player_id=$studioPlayer WHERE player_id=$pId";
		}
		foreach ($sql as $q)
		{
			$this->DbQuery($q);
		}

		$this->reloadPlayersBasicInfos();
	}
//
// Debug functions
//
}
