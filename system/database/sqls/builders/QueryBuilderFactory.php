<?php
namespace System\Database\Sqls\builders;
/**
 * Query Builder Factory
 */
class QueryBuilderFactory
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
