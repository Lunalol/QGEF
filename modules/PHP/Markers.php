<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class Markers extends APP_GameClass
{
	static $table = null;
	static function create(string $type, int $location)
	{
		self::$table->DbQuery("INSERT INTO markers (type,location) VALUES ('$type',$location)");
	}
	static function getAllDatas(): array
	{
		return self::$table->getObjectListFromDB("SELECT * FROM markers ORDER BY type");
	}
	static function get(string $type): array
	{
		return self::$table->getNonEmptyObjectFromDB("SELECT * FROM markers WHERE type = '$type'");
	}
	static function getLocation(string $type): string
	{
		return self::$table->getUniqueValueFromDB("SELECT location FROM markers WHERE type = '$type'");
	}
	static function setLocation(string $type, int $location): void
	{
		self::$table->dbQuery("UPDATE markers SET location = $location WHERE type = '$type'");
	}
}
