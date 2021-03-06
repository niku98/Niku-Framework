<?php
namespace System\Database;
use System\Database\Sqls\Database as SqlDatabase;
use System\Database\NoSqls\Database as NoSqlDatabase;

/**
 * Class Database for connect to database
 * Just use for MySql Prepare
 */

class Database
{

	/**
	 * Make Constructor private
	 *
	 * @param	  void
	 * @return    void
	 */
	private function __construct()
	{
	}


	/**
	 * Static method to create Database object depend on DB_TYPE
	 *
	 * @param	  string $table
	 * @return    NoSqlDatabase/SqlDatabase
	 */
	public static function __callStatic($method, $params)
	{
		$type = app()->env('DB_TYPE');

		switch ($type) {
			case 'sql':
				return (new SqlDatabase())->$method(...$params);
				break;
			case 'nosql':
				return (new NoSqlDatabase())->$method(...$params);
			default:
				throw new AppException("Database type [$type] is not supported!");
				break;
		}
	}
}


 ?>
