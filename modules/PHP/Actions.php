<?php

/**
 * Description of action
 *
 * @author Lunalol
 */
class Actions extends APP_GameClass
{
	static function clear()
	{
		self::dBQuery("DELETE FROM actions");
	}
	static function remove(int $id)
	{
		self::dBQuery("DELETE FROM actions WHERE id = $id");
	}
	static function add(string $status, array $data)
	{
		$json = self::escapeStringForDB(json_encode($data, JSON_FORCE_OBJECT));
		self::DbQuery("INSERT INTO actions (status,data) VALUES ('$status', '$json')");
		return self::DbGetLastId();
	}
	static function empty()
	{
		return boolval(self::getUniqueValueFromDB("SELECT EXISTS (SELECT * FROM actions)"));
	}
	static function getStatus(int $id)
	{
		return self::getUniqueValueFromDB("SELECT status FROM actions WHERE id = $id");
	}
	static function setStatus(int $id, string $status)
	{
		return self::dBquery("UPDATE actions SET status = '$status' WHERE id = $id");
	}
	static function get(int $id)
	{
		return json_decode(self::getUniqueValueFromDB("SELECT data FROM actions WHERE id = $id"), JSON_OBJECT_AS_ARRAY);
	}
	static function cancel()
	{
		return self::getUniqueValueFromDB("SELECT id FROM actions WHERE status = 'done' ORDER BY id DESC LIMIT 1");
	}
	static function action()
	{
		return self::getUniqueValueFromDB("SELECT id FROM actions WHERE status = 'pending' ORDER BY id ASC LIMIT 1");
	}
}
