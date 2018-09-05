<?php
namespace system\model;
use system\model\relations\Factory as RelationFactory;
/**
 *
 */
trait HasRelation
{
	public function hasOne(string $model, string $foreign_key = '', string $local_key = ''){
		return RelationFactory::create('HasOne', $this, new $model(), $local_key, $foreign_key);
	}

	/*
	* Params: [
	*	model => Target model to get
	*	foreign_key => Name of column in target model point to local_key
	*]
	*
	*/
	public function hasMany(string $model, string $foreign_key = '', string $local_key = ''){
		return RelationFactory::create('HasMany', $this, new $model(), $local_key, $foreign_key);
	}

	/*
	* Params: [
	*	model => Target model to get
	*	foreign_key => Name of column in current model point to local_key of target model
	*]
	*
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
	 * @return    array
	 */
	public function belongsToMany(string $model, string $tableMid = '', string $this_foreign_key = '', string $target_foreign_key = ''){
		return RelationFactory::create('BelongsToMany', $this, new $model(), $this_foreign_key, $target_foreign_key, $tableMid);
	}
}



 ?>
