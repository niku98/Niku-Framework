<?php
namespace System\Model\Relations;
use AppException;
/**
 * Has One - Relation
 */
class HasMany extends Relation
{
	public function first()
	{
		return $this->subModel->first();
	}


	public function associate($object)
	{
		if(!is_a($object, get_class($this->subModel), true)){
			throw new AppException('Model need to associate must be an instance of '.get_class($this->subModel));
		}

		$object->{$this->subKey} = $this->getMainModelKeyVal();
		$object->save();
		return $this;
	}

	public function dissociate()
	{
		$object = func_get_args()[0];
		if(!is_a($object, get_class($this->subModel), true)){
			throw new AppException('Model need to dissociate must be an instance of '.get_class($this->subModel));
		}

		$object->{$this->subKey} = null;
		$object->save();
		return $this;
	}

	protected function processConditionInRelation(){
		$this->subModel = $this->subModel->where($this->subKey, $this->getMainModelKeyVal());
		return $this;
	}

	protected function getResult(){
		return $this->subModel->get();
	}

	protected function processInsertData(){
		$data = func_get_args()[0];
		if(is_array($data) && array_key_exists(0, $data)){
			return $this->processInsertManyData($data);
		}else{
			return $this->processInsertOneData($data);
		}
	}

	protected function processInsertOneData($data)
	{
		if(is_object($data) && is_a($data, 'System\Model\Model', true)){
			$data = $data->toArray();
		}

		$data[$this->subKey] = $this->getMainModelKeyVal();
		return $data;
	}

	protected function processInsertManyData($data)
	{
		$result = array();
		foreach ($data as $key => $value) {
			$result[] = $this->processInsertOneData($value);
		}

		return $result;
	}
}

 ?>
