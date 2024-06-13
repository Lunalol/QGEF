<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class Pieces extends APP_GameClass
{
	const ALL = 0;
#
	const INFANTRY = 'infantry';
	const TANK = 'tank';
	const AIRPLANE = 'airplane';
	const FLEET = 'fleet';
#
	const RANK = [null => 0, self::INFANTRY => 0, self::TANK => 1, self::AIRPLANE => 2, self::FLEET => 2, 'StandFast' => 3, 'SustainAttack' => 3];
#
	const PIECES = [
		Factions::SOVIETUNION => [self::INFANTRY => 14, self::TANK => 5, self::AIRPLANE => 4, self::FLEET => 3],
		Factions::GERMANY => [self::INFANTRY => 7, self::TANK => 5, self::AIRPLANE => 5, self::FLEET => 2],
		Factions::PACT => [self::INFANTRY => 6, self::TANK => 1, self::AIRPLANE => 2, self::FLEET => 1],
	];
#
	const STARTING = [
		Factions::ALLIES => [
			Factions::SOVIETUNION => [
				self::INFANTRY => [KARELIA, BALTICSTATES, BREST, LWOW, BESSARABIA, SEVASTOPOL],
				self::TANK => [MOSCOW, KIEV],
				self::AIRPLANE => [LENINGRAD, SMOLENSK, ROSTOV],
				self::FLEET => [GULFOFFINLAND, BLACKSEA],
			],
		],
		Factions::AXIS => [
			Factions::GERMANY => [
				self::INFANTRY => [BERLIN, EASTPRUSSIA, WARSAW, WARSAW, ROMANIA],
				self::TANK => [EASTPRUSSIA, WARSAW, WARSAW],
				self::AIRPLANE => [BERLIN, EASTPRUSSIA, WARSAW, WARSAW],
				self::FLEET => [WESTBALTICSEA],
			],
			Factions::PACT => [
				self::INFANTRY => [HUNGARY, YUGOSLAVIA, FINLAND, ROMANIA],
				self::TANK => [ROMANIA],
				self::AIRPLANE => [FINLAND, ROMANIA],
				self::FLEET => [],
			],
		],
	];
	static $table = null;
	static function create(string $player, string $FACTION, string $TYPE, int $location, $status = [])
	{
//		$json = self::$table->escapeStringForDB(json_encode($status, JSON_FORCE_OBJECT));
		$json = json_encode($status, JSON_FORCE_OBJECT);
		self::$table->DbQuery("INSERT INTO pieces (player,faction,type,location,status) VALUES ('$player','$FACTION','$TYPE',$location,'$json')");
		return self::$table->DbGetLastId();
	}
	static function destroy(int $id): void
	{
		self::$table->DbQuery("DELETE FROM pieces WHERE id = $id");
	}
	static function getAllDatas(): array
	{
		return self::$table->getObjectListFromDB("SELECT id,player,faction,type,location FROM pieces ORDER BY player,faction,type");
	}
	static function getAll(string $player, string $status = null, string $value = null): array
	{
		if ($status) return self::$table->getObjectListFromDB("SELECT id,player,faction,type,location FROM pieces WHERE player = '$player' AND JSON_UNQUOTE(status->'$.$status') = '$value' ORDER BY faction,type");
		return self::$table->getObjectListFromDB("SELECT id,player,faction,type,location FROM pieces WHERE player = '$player' ORDER BY faction,type");
	}
	static function get(int $id, bool $status = false): array
	{
		if ($status) return self::$table->getNonEmptyObjectFromDB("SELECT id,player,faction,type,location,status FROM pieces WHERE id = $id");
		return self::$table->getNonEmptyObjectFromDB("SELECT id,player,faction,type,location FROM pieces WHERE id = $id");
	}
	static function getAtLocation(int $location, string $faction = ''): array
	{
		if ($faction) return self::$table->getCollectionFromDB("SELECT * FROM pieces WHERE location = $location AND faction = '$faction' ORDER BY type");
		return self::$table->getCollectionFromDB("SELECT * FROM pieces WHERE location = $location ORDER BY faction,type");
	}
	static function getLocation(int $id): string
	{
		return self::$table->getUniqueValueFromDB("SELECT location FROM pieces WHERE id = $id");
	}
	static function setLocation(int $id, int $location): void
	{
		self::$table->dbQuery("UPDATE pieces SET location = $location WHERE id = $id");
	}
	static function getStatus(int $id, string $status)
	{
		return json_decode(self::$table->getUniqueValueFromDB("SELECT JSON_UNQUOTE(status->'$.$status') FROM pieces WHERE id = $id"), JSON_OBJECT_AS_ARRAY);
	}
	static function setStatus(int $id, string $status, $value = null): void
	{
		if (is_null($value)) $sql = "UPDATE pieces SET status = JSON_REMOVE(status,'$.$status')";
		else $sql = "UPDATE pieces SET status = JSON_SET(status,'$.$status','$value')";
		if ($id !== self::ALL) $sql .= " WHERE id = $id";
		self::$table->dbQuery($sql);
	}
	static function getEnnemyControled(string $player): array
	{
		return self::$table->getObjectListFromDB("SELECT DISTINCT location FROM pieces WHERE player <> '$player'", true);
	}
	static function count(string $faction)
	{
		return self::$table->getCollectionFromDB("SELECT type, count(*) FROM pieces WHERE faction = '$faction' GROUP BY type", true);
	}
	static function getPossibleMoves(string $FACTION, array $pieces): array
	{
		$ennemies = self::getEnnemyControled($FACTION);
		$control = Board::getControl($FACTION, 'startOfStep');
#
		$possibles = [];
		foreach ($pieces as $piece)
		{
			$possibles[$piece['id']] = [+$piece['location']];
#
			switch ($piece['type'])
			{
#
				case self::INFANTRY:
				case self::TANK:
#
					foreach (Board::ADJACENCY[$piece['location']] as $next_location)
					{
						# Infantry and tanks may only move to land spaces
						if (Board::REGIONS[$next_location]['type'] === LAND)
						{
							# You may never move a piece to a space occupied by an enemy piece
							if (!in_array($next_location, $ennemies)) $possibles[$piece['id']][] = $next_location;
						}
					}
					break;
#
				case self::AIRPLANE:
#
					foreach (Board::ADJACENCY[$piece['location']] as $next_location)
					{
						# Airplanes may only move to land spaces
						if (Board::REGIONS[$next_location]['type'] === LAND)
						{
							# You may never move a piece to a space occupied by an enemy piece
							if (!in_array($next_location, $ennemies))
							{
								# An airplane cannot end its move in a space that you did not control at the beginning of the step
								if (in_array($next_location, $control)) $possibles[$piece['id']][] = $next_location;
							}
						}
						# They can move up to 2 spaces
						foreach (Board::ADJACENCY[$next_location] as $next_next_location)
						{
							if (!in_array($next_next_location, $possibles[$piece['id']]))
							{
								# Infantry, tanks, and airplanes may only move to land spaces
								if (Board::REGIONS[$next_next_location]['type'] === LAND)
								{
									# You may never move a piece to a space occupied by an enemy piece
									if (!in_array($next_next_location, $ennemies))
									{
										# An airplane cannot end its move in a space that you did not control at the beginning of the step
										if (in_array($next_next_location, $control)) $possibles[$piece['id']][] = $next_next_location;
									}
								}
							}
						}
					}
					break;
#
				case self::FLEET:
#
					foreach (Board::ADJACENCY[$piece['location']] as $next_location)
					{
						# Fleets may only move to water spaces
						if (Board::REGIONS[$next_location]['type'] === WATER)
						{
							# You may never move a piece to a space occupied by an enemy piece
							if (!in_array($next_location, $ennemies)) $possibles[$piece['id']][] = $next_location;
						}
					}
					break;
#
			}
		}
#
		return $possibles;
	}
	static function getPossibleRetreats(string $FACTION, array $pieces): array
	{
		$ennemies = self::getEnnemyControled($FACTION);
		$control = Board::getControl($FACTION);
#
		$possibles = [];
		foreach ($pieces as $piece)
		{
#			$possibles[$piece['id']] = [+$piece['location']];
#
			switch ($piece['type'])
			{
#
				case self::INFANTRY:
				case self::TANK:
				case self::AIRPLANE:
#
					foreach (Board::ADJACENCY[$piece['location']] as $next_location)
					{
						# Infantry, tanks and airplanes may only move to land spaces
						if (Board::REGIONS[$next_location]['type'] === LAND)
						{
							# You may never move a piece to a space occupied by an enemy piece
							if (!in_array($next_location, $ennemies))
							{
								# The retreating piece must move to a space you currently control
								if (in_array($next_location, $control)) $possibles[$piece['id']][] = $next_location;
							}
						}
					}
					break;
#
				case self::FLEET:
#
					throw new BgaVisibleSystemException("No retreat for fleets");
#
			}
		}
#
		return $possibles;
	}
	static function getPossibleAttacks(string $FACTION, array $pieces, array $into = []): array
	{
		$ennemies = self::getEnnemyControled($FACTION);
		$control = Board::getControl($FACTION);
#
		$possibles = [];
		foreach ($pieces as $piece)
		{
			if ($piece['type'] == self::INFANTRY || $piece['type'] == self::TANK)
			{
				$locations = [];
				foreach (Board::ADJACENCY[$piece['location']] as $next_location)
				{
					if ($into && !in_array($next_location, $into)) continue;
//
					# Infantry and tanks may only move to land spaces
					if (Board::REGIONS[$next_location]['type'] === LAND)
					{
						if (in_array($next_location, $ennemies)) $locations[] = $next_location;
						if (!self::getAtLocation($next_location) && !in_array($next_location, $control)) $locations[] = $next_location;
					}
				}
#
				if ($locations) $possibles[$piece['id']] = $locations;
			}
		}
#
		return $possibles;
	}
	static function getInRange(int $location, int $range, array $pieces)
	{
		$possibles = [];
		foreach ($pieces as $piece)
		{
			if ($range >= 1)
			{
				$next_locations = Board::ADJACENCY[$location];
				if (!in_array($piece['location'], $next_locations))
				{
					if ($range >= 2)
					{
						foreach (Board::ADJACENCY[$location] as $next_location)
						{
							if (in_array($piece['location'], Board::ADJACENCY[$next_location]))
							{
								$possibles[] = $piece['id'];
								break;
							}
						}
					}
				}
				else $possibles[] = $piece['id'];
			}
		}
#
		return $possibles;
	}
}
