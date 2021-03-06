<?php
namespace System\Model;
use System\Model\Relations\Factory as RelationFactory;
/**
 *
 */
trait HasRelation
{
	/**
	 * HasOne Relationship
	 *
	 * @param     string $model
	 * @param     string $foreign_key
	 * @param     string $local_key
	 * @return    Relation
	 */
	public function hasOne(string $model, string $foreign_key = '', string $local_key = ''){
		return RelationFactory::create('HasOne', $this, new $model(), $foreign_key, $local_key);
	}

	/**
	 * HasMany Relationship
	 *
	 * @param     string $model
	 * @param     string $foreign_key
	 * @param     string $local_key
	 * @return    Relation
	 */
	public function hasMany(string $model, string $foreign_key = '', string $local_key = ''){
		return RelationFactory::create('HasMany', $this, new $model(), $foreign_key, $local_key);
	}

	/**
	 * Belongs To Relationship
	 *
	 * @param     string $model
	 * @param     string $foreign_key
	 * @param     string $local_key
	 * @return    Relation
	 */
	public function belongsTo(string $model, string $foreign_key = '', string $local_key = ''){
		return RelationFactory::create('BelongsTo', $this, new $model(), $foreign_key, $local_key);
	}

	/**
	 * belongs To Many Relationship
	 *
	 * @param     string $model
	 * @param     string $tableMid
	 * @param     string $this_foreign_key
	 * @param     string $target_foreign_key
	 * @return    Relation
	 */
	public function belongsToMany(string $model, string $tableMid = '', string $this_foreign_key = '', string $target_foreign_key = ''){
		return RelationFactory::create('BelongsToMany', $this, new $model(), $this_foreign_key, $target_foreign_key, $tableMid);
	}

	/**
	 * has Many Relationship
	 *
	 * @param     string $midModel
	 * @param     string $lastModel
	 * @param     string $midForeignKey
	 * @param     string $lastForeignKey
	 * @param     string $mainPriKey
	 * @param     string $midForeignKey
	 * @return    HasManyThrough
	 */
	public function hasManyThrough(string $midModel, string $lastModel, string $midForeignKey, string $lastForeignKey, string $mainPriKey = '', string $midPriKey = ''){
		return RelationFactory::create('HasManyThrough', $this, new $midModel(), new $lastModel, $midForeignKey, $lastForeignKey, $mainPriKey, $midPriKey);
	}
}



 ?>
