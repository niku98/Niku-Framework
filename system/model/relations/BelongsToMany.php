<?php
namespace System\Model\Relations;
use Database;

/**
 * Belongs To Many - Relation
 */
class BelongsToMany extends Relation
{
	public function first()
	{
		$this->processConditionInRelation();
		return $this->subModel->first();
	}

	public function insert()
	{
		$data = func_get_args()[0];
		if(is_array($data) && array_key_exists(0, $data)){
			return $this->insertMany($data);
		}else{
			return $this->insertOne($data);
		}
	}

	public function insertOne($data)
	{
		$inserted_id = parent::insert($data)->insert_id();

		Database::table($this->middleTable)->insert([
			$this->subKey => $inserted_id,
			$this->mainKey => $this->mainModel->getPrimaryKeyVal()
		]);

		return $inserted_id;
	}

	public function insertMany($data)
	{
		$result = array();
		foreach ($data as $item ) {
			$result[] = $this->insertOne($item)->insert_id();
		}

		return $result;
	}

	protected function processConditionInRelation(){
		$this->subModel = $this->subModel->where($this->subModel->getPrimaryKey(), 'IN', $this->getListSubIds());
		return $this;
	}

	protected function getResult(){
		return $this->subModel->get();
	}

	protected function processInsertData(){
		$data = func_get_args()[0];
		if(is_object($data) && is_a($data, 'System\Model\Model', true)){
			$data = $data->toArray();
		}

		return $data;
	}

	private function getListSubIds()
	{
		$list = Database::table($this->middleTable)->select($this->subKey)
		->where($this->mainKey, $this->mainModel->getPrimaryKeyVal())->get();

		$subIds = array();
		foreach ($list as $k) {
			$subIds = array_merge($subIds, array_values($k));
		}

		return $subIds;
	}
}



 ?>
