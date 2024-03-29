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
	function create(string $player, string $FACTION, string $TYPE, int $location, $status = [])
	{
		$json = self::escapeStringForDB(json_encode($status, JSON_FORCE_OBJECT));
		self::DbQuery("INSERT INTO pieces (player,faction,type,location,status) VALUES ('$player','$FACTION','$TYPE',$location,'$json')");
		return self::DbGetLastId();
	}
	function destroy(int $id): void
	{
		self::DbQuery("DELETE FROM pieces WHERE id = $id");
	}
	function getAllDatas(): array
	{
		return self::getObjectListFromDB("SELECT id,player,faction,type,location FROM pieces ORDER BY player,faction,type");
	}
	function getAll(string $player, string $status = null, string $value = null): array
	{
		if ($status) return self::getObjectListFromDB("SELECT id,player,faction,type,location FROM pieces WHERE player = '$player' AND JSON_UNQUOTE(status->'$.$status') = '$value' ORDER BY faction,type");
		return self::getObjectListFromDB("SELECT id,player,faction,type,location FROM pieces WHERE player = '$player' ORDER BY faction,type");
	}
	function get(int $id, bool $status = false)
	{
		if ($status) return self::getNonEmptyObjectFromDB("SELECT id,player,faction,type,location,status FROM pieces WHERE id = $id");
		return self::getNonEmptyObjectFromDB("SELECT id,player,faction,type,location FROM pieces WHERE id = $id");
	}
	static function getAtLocation(int $location)
	{
		return self::getCollectionFromDB("SELECT * FROM pieces WHERE location = $location ORDER BY faction,type");
	}
	static function setLocation(int $id, int $location): void
	{
		self::dbQuery("UPDATE pieces SET location = $location WHERE id = $id");
	}
	static function getStatus(int $id, string $status)
	{
		return json_decode(self::getUniqueValueFromDB("SELECT JSON_UNQUOTE(status->'$.$status') FROM pieces WHERE id = $id"), JSON_OBJECT_AS_ARRAY);
	}
	static function setStatus(int $id, string $status, $value = null): void
	{
		if (is_null($value)) $sql = "UPDATE pieces SET status = JSON_REMOVE(status,'$.$status')";
		else $sql = "UPDATE pieces SET status = JSON_SET(status,'$.$status','$value')";
		if ($id !== self::ALL) $sql .= " WHERE id = $id";
		self::dbQuery($sql);
	}
	function getEnnemyControled(string $player): array
	{
		return self::getObjectListFromDB("SELECT DISTINCT location FROM pieces WHERE player <> '$player'", true);
	}
	function getSupply(string $FACTION)
	{
		return array_keys(board::REGIONS);
	}
	function getPossibleMoves(string $FACTION, array $pieces): array
	{
		$ennemies = self::getEnnemyControled($FACTION);
		$control = Factions::getControl($FACTION);
#
		$supply = [];
		foreach (Factions::FACTIONS[$FACTION] as $faction) $supply[$faction] = self::getSupply($faction);
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
							if (!in_array($next_location, $ennemies))
							{
								if (in_array($next_location, $supply[$piece['faction']])) $possibles[$piece['id']][] = $next_location;
							}
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
								# You may not move a piece to a space where it would be unsupplied at the moment it moves there
								if (in_array($next_location, $supply[$piece['faction']]))
								{
									# An airplane cannot end its move in a space that you did not control at the beginning of the step
									if (in_array($next_location, $control)) $possibles[$piece['id']][] = $next_location;
								}
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
										# You may not move a piece to a space where it would be unsupplied at the moment it moves there
										if (in_array($next_next_location, $supply[$piece['faction']]))
										{
											# An airplane cannot end its move in a space that you did not control at the beginning of the step
											if (in_array($next_next_location, $control)) $possibles[$piece['id']][] = $next_next_location;
										}
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
							if (!in_array($next_location, $ennemies))
							{
								# You may not move a piece to a space where it would be unsupplied at the moment it moves there
								if (in_array($next_location, $supply[$piece['faction']])) $possibles[$piece['id']][] = $next_location;
							}
						}
					}
					break;
#
			}
		}
#
		return $possibles;
	}
	function getPossibleRetreats(string $FACTION, array $pieces): array
	{
		$ennemies = self::getEnnemyControled($FACTION);
		$control = Factions::getControl($FACTION);
#
		$supply = [];
		foreach (Factions::FACTIONS[$FACTION] as $faction) $supply[$faction] = self::getSupply($faction);
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
								if (in_array($next_location, $supply[$piece['faction']]))
								{
									# The retreating piece must move to a space you currently control
									if (in_array($next_location, $control)) $possibles[$piece['id']][] = $next_location;
								}
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
	function getPossibleAttacks(string $FACTION, array $pieces): array
	{
		$ennemies = self::getEnnemyControled($FACTION);
#
		$possibles = [];
		foreach ($pieces as $piece)
		{
			if ($piece['type'] == self::INFANTRY || $piece['type'] == self::TANK)
			{
				$locations = [];
				foreach (Board::ADJACENCY[$piece['location']] as $next_location)
				{
					# Infantry and tanks may only move to land spaces
					if (Board::REGIONS[$next_location]['type'] === LAND && in_array($next_location, $ennemies)) $locations[] = $next_location;
				}
#
				if ($locations) $possibles[$piece['id']] = $locations;
			}
		}
#
		return $possibles;
	}
}
