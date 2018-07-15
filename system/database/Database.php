<?php
namespace system\database;
use system\database\sqls\Database as SqlDatabase;
use system\database\nosqls\Database as NoSqlDatabase;

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
	public static function table()
	{
		global $_CONFIG;
		$type = $_CONFIG['DB_TYPE'];

		switch ($type) {
			case 'sql':
				return new SqlDatabase(...func_get_args());
				break;
			case 'nosql':
				return new NoSqlDatabase(...func_get_args());
			default:
				throw new AppException("Database type [$type] is not supported!");
				break;
		}
	}
}


 ?>
