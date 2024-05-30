<?php

/**
 * Description of action
 *
 * @author Lunalol
 */
class Actions extends APP_GameClass
{
	static $table = null;
	static function clear()
	{
		self::$table->DbQuery("DELETE FROM actions");
	}
	static function remove(int $id)
	{
		self::$table->DbQuery("DELETE FROM actions WHERE id = $id");
	}
	static function add(string $status, array $data)
	{
//		$json = self::$table->escapeStringForDB(json_encode($data, JSON_FORCE_OBJECT));
		$json = json_encode($data, JSON_FORCE_OBJECT);
		self::$table->DbQuery("INSERT INTO actions (status,data) VALUES ('$status', '$json')");
		return self::$table->DbGetLastId();
	}
	static function empty()
	{
		return boolval(self::$table->getUniqueValueFromDB("SELECT EXISTS (SELECT * FROM actions)"));
	}
	static function getStatus(int $id)
	{
		return self::$table->getUniqueValueFromDB("SELECT status FROM actions WHERE id = $id");
	}
	static function setStatus(int $id, string $status)
	{
		return self::$table->DbQuery("UPDATE actions SET status = '$status' WHERE id = $id");
	}
	static function get(int $id)
	{
		return json_decode(self::$table->getUniqueValueFromDB("SELECT data FROM actions WHERE id = $id"), JSON_OBJECT_AS_ARRAY);
	}
	static function getPlayedLocations()
	{
		return self::$table->getObjectListFromDB("SELECT JSON_UNQUOTE(data->'$.piece.location') FROM actions WHERE status = 'undo' AND data->'$.name' <> 'remove'", true);
	}
	static function getPlayedPieces()
	{
		return self::$table->getCollectionFromDB("SELECT id,player,faction,type,location FROM pieces WHERE id IN (SELECT JSON_UNQUOTE(data->'$.piece.id') FROM actions WHERE status = 'undo' AND data->'$.name' <> 'remove')");
	}
	static function getLastUndo()
	{
		return self::$table->getUniqueValueFromDB("SELECT id FROM actions WHERE status = 'undo' ORDER BY id DESC LIMIT 1");
	}
	static function getLastAction()
	{
		return self::$table->getUniqueValueFromDB("SELECT id FROM actions WHERE status = 'done' ORDER BY id DESC LIMIT 1");
	}
	static function getNextAction()
	{
		return self::$table->getUniqueValueFromDB("SELECT id FROM actions WHERE status = 'pending' ORDER BY id ASC LIMIT 1");
	}
}
