<?php
namespace System\Database\Migration;
use System\Database\Migration\BluePrints\MysqlBluePrint;

/**
 *
 */
class BluePrint
{
	private function __construct()
	{
		// code...
	}

	public static function create($table)
	{
		$dbType = strtolower(env('DB_CONNECTION', 'mysql'));
		switch ($dbType) {
			case 'mysql':
				return new MysqlBluePrint($table);
				break;

			default:
				// code...
				break;
		}
	}
}


 ?>
