<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class Factions extends APP_GameClass
{
	const ALLIES = 'allies';
	const AXIS = 'axis';
//
	const SOVIETUNION = 'sovietUnion';
	const GERMANY = 'germany';
	const PACT = 'pact';
//
	const FACTIONS = [
		self::ALLIES => [self::SOVIETUNION],
		self::AXIS => [self::GERMANY, self::PACT]
	];
//
// Colors (3)
//
	const COLORS = [
		self::SOVIETUNION => 'be1e1e',
		self::GERMANY => '4d514d',
		self::PACT => '6e6864',
	];
//
	static function create(string $FACTION, int $player_id): int
	{
		self::DbQuery("INSERT INTO factions (faction, player_id,status) VALUES ('$FACTION', $player_id, '{}')");
		return self::DbGetLastId();
	}
	static function getAllDatas(): array
	{
		return self::getCollectionFromDb("SELECT faction, player_id, VP FROM factions");
	}
	static function getPlayerID(string $FACTION): int
	{
		return intval(self::getUniqueValueFromDB("SELECT player_id FROM factions WHERE faction = '$FACTION'"));
	}
	static function setActivation(string $FACTION = 'ALL', string $activation = 'no'): void
	{
		if ($FACTION === 'ALL') self::dbQuery("UPDATE factions SET activation = '$activation'");
		else self::dbQuery("UPDATE factions SET activation = '$activation' WHERE faction = '$FACTION'");
	}
	static function getActivation(string $FACTION): string
	{
		return self::getUniqueValueFromDB("SELECT activation FROM factions WHERE faction = '$FACTION'");
	}
	static function getActive(): string
	{
		return self::getUniqueValueFromDB("SELECT faction FROM factions WHERE activation = 'yes'");
	}
	static function getInactive(): string
	{
		return self::getUniqueValueFromDB("SELECT faction FROM factions WHERE activation <> 'yes'");
	}
	static function getStatus(string $FACTION, string $status)
	{
		return json_decode(self::getUniqueValueFromDB("SELECT JSON_UNQUOTE(status->'$.$status') FROM factions WHERE faction = '$FACTION'"), JSON_OBJECT_AS_ARRAY);
	}
	static function setStatus(string $FACTION, string $status, $value = null): void
	{
		if (is_null($value)) self::dbQuery("UPDATE factions SET status = JSON_REMOVE(status, '$.$status') WHERE faction = '$FACTION'");
		else
		{
			$json = self::escapeStringForDB(json_encode($value));
			self::dbQuery("UPDATE factions SET status = JSON_SET(status, '$.$status', '$json') WHERE faction = '$FACTION'");
		}
	}
	static function getVP(string $FACTION): int
	{
		return intval(self::getUniqueValueFromDB("SELECT VP FROM factions WHERE faction = '$FACTION'"));
	}
	static function incVP(string $FACTION, int $VP): int
	{
		self::dbQuery("UPDATE factions SET VP = VP + $VP WHERE faction = '$FACTION'");
		return self::getVP($FACTION);
	}
	function updateControl(): void
	{
		self::dbQuery("UPDATE control SET player = 'both' WHERE terrain = 'water'");
		foreach (array_keys(self::FACTIONS) as $FACTION) self::dbQuery("UPDATE control SET player = '$FACTION' WHERE location IN (SELECT DISTINCT location FROM pieces WHERE player = '$FACTION')");
	}
	function getControl(string $FACTION): array
	{
		return self::getObjectListFromDB("SELECT location FROM control WHERE player IN ('both', '$FACTION')", true);
	}
}
