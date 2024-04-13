<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class Markers extends APP_GameClass
{
	static function create(string $type, int $location)
	{
		self::DbQuery("INSERT INTO markers (type,location) VALUES ('$type',$location)");
	}
	static function getAllDatas(): array
	{
		return self::getObjectListFromDB("SELECT * FROM markers ORDER BY type");
	}
	function get(string $type): array
	{
		return self::getNonEmptyObjectFromDB("SELECT * FROM markers WHERE type = '$type'");
	}
	function getLocation(string $type): string
	{
		return self::getUniqueValueFromDB("SELECT location FROM markers WHERE type = '$type'");
	}
	function setLocation(string $type, int $location): void
	{
		self::dbQuery("UPDATE markers SET location = $location WHERE type = '$type'");
	}
}
