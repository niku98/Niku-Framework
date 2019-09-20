<?php
namespace System\Model\Relations;
/**
 * RelationFactory
 */
class Factory
{
	public static function create()
	{
		$params = func_get_args();
		$name = $params[0];
		unset($params[0]);
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
			case 'HasManyThrough':
				return new HasManyThrough(...$params);
				break;
			default:
				throw new AppException("[$name] Relation is not exists!");
		}
	}
}


 ?>
