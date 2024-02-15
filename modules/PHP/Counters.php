<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class Counters extends APP_GameClass
{
	function create(string $type, int $location)
	{
		self::DbQuery("INSERT INTO counters (type,location) VALUES ('$type',$location)");
		return self::DbGetLastId();
	}
	function get(int $id)
	{
		return self::getNonEmptyObjectFromDB("SELECT type,location FROM counters WHERE id = $id");
	}
	function getAllDatas(): array
	{
		return self::getObjectListFromDB("SELECT id,type,location FROM counters ORDER BY type");
	}
}
