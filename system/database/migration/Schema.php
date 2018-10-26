<?php
namespace System\database\Migration;
use System\database\Database;
/**
*
*/
class Schema
{

	public static $db;

	private static function getDatabase()
	{
		if(!static::$db){
			static::$db = Database::table('information_schema.tables');
		}

		return static::$db;
	}

	public static function hasTable(string $table)
	{
		$db = static::getDatabase();
		return (bool)$db->where('table_schema', app()->config('DB_DATABASE'))->where('table_name', $table)->count();
	}

	public static function create(string $name, $callable)
	{
		if(!is_callable($callable)){
			throw new \AppException('Parameter for Schema::create function must be callable!');
		}

		$table = new BluePrint($name);
		$callable($table);

		$table->design();
	}

	public static function table(string $name, $callable)
	{
		if(!is_callable($callable)){
			throw new \AppException('Parameter for Schema::create function must be callable!');
		}

		$table = new BluePrint($name);
		$callable($table);

		$table->alter();
	}

	public static function drop(string $name)
	{
		$table = new BluePrint($name);
		$table->drop();
	}

	public static function dropIfExists(string $name)
	{
		if(static::hasTable($name)){
			static::drop($name);
		}
	}


}
?>
