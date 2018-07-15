<?php
namespace system\database\sqls\builders;
use system\patterns\Factory;
/**
 * Query Builder Factory
 */
class QueryBuilderFactory implements Factory
{

	public static function create()
	{
		$driver = func_get_args()[0];
		switch ($driver) {
			case 'mysql':
				return new MysqlBuilder();
				break;

			default:
				throw new AppException("Database driver [$driver] is not supported!");
				break;
		}
	}
}


 ?>
