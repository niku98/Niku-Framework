<?php
namespace system\model\relations;
use system\patterns\Factory as FactoryPattern;
/**
 * RelationFactory
 */
class Factory implements FactoryPattern
{
	public static function create()
	{
		$params = func_get_args();
		$name = $params[0];
		unset($params[0]);
		var_dump($params);
		switch ($name) {
			case 'HasOne':
				return new HasOne(...$params);
				break;

			case 'HasMany':
				return new HasMany(...$params);
				break;

			case 'BelongsToMany':
				return new BelongsToMany(...$params);
				break;

			case 'BelongsTo':
				return new BelongsTo(...$params);
				break;
		}
	}
}


 ?>
