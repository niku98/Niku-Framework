<?php
namespace System\Database\Sqls\connections;
use System\Database\Sqls\connections\MysqlConnection;
use AppException;

/**
 * Connection Factory
 */
class ConnectionFactory
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
