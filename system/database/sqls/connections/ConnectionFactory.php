<?php
namespace system\database\sqls\connections;
use system\patterns\Factory;
use system\database\sqls\connections\MysqlConnection;
use AppException;

/**
 * Connection Factory
 */
class ConnectionFactory implements Factory
{

	public static function create()
	{
		$driver = func_get_args()[0];
		$database = func_get_args()[1];
		switch ($driver) {
			case 'mysql':
				return new MysqlConnection($database);
				break;

			default:
				throw new AppException("Database driver [$driver] is not supported!");
				break;
		}
	}
}


 ?>
