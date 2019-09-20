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
		if($this->belongsToNull){
			return null;
		}
		return $this->subModel->first();
	}

	public function all()
	{
		return parent::get();
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

		$this->attachOne($inserted_id);

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
		$list_id = $this->getListSubIds();
		if(count($list_id) == 0){
			$this->belongsToNull = true;
		}else{
			$this->belongsToNull = false;
			$this->subModel = $this->subModel->where($this->subModel->getPrimaryKey(), 'IN', $list_id);
		}

		return $this;
	}

	protected function getResult(){
		if($this->belongsToNull){
			return new \System\Supporters\Collection([]);
		}
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
		$list = Database::table($this->middleTable)->distinct()->select($this->subKey)
		->where($this->mainKey, $this->mainModel->getPrimaryKeyVal())->get();

		$subIds = array();
		foreach ($list as $k) {
			$subIds = array_merge($subIds, array_values($k));
		}

		if(empty($subIds)){
			$subIds = [-1];
		}

		return $subIds;
	}

	public function attach($id)
	{
		if(is_array($id)){
			return $this->attachMany($id);
		}else{
			return $this->attachOne($id);
		}
	}

	protected function attachMany($list_id)
	{
		foreach ($list_id as $id) {
			$this->attachOne($id);
		}
	}

	protected function attachOne($inserted_id)
	{
		try {
			Database::table($this->middleTable)->insert([
				$this->subKey => $inserted_id,
				$this->mainKey => $this->mainModel->getPrimaryKeyVal()
			]);
			return true;
		} catch (\Exception $e) {
			return false;
		}

	}

	public function detach($id = [])
	{
		if(empty($id)){
			$id = $this->getListSubIds();
		}

		if(is_array($id)){
			return $this->detachMany($id);
		}else{
			return $this->detachOne($id);
		}
	}

	protected function detachMany($list_id)
	{
		foreach ($list_id as $id) {
			$this->detachOne($id);
		}

		return $this;
	}

	protected function detachOne($inserted_id)
	{
		try {
			Database::table($this->middleTable)->where($this->subKey, $inserted_id)
			->where($this->mainKey, $this->mainModel->getPrimaryKeyVal())->delete();
			return $this;
		} catch (\Exception $e) {
			return false;
		}

	}
}



 ?>
