<?php
namespace System\Model\Relations;

/**
 * Has One - Relation
 */
class HasOne extends HasMany
{
	protected function getResult(){
		return $this->subModel->first();
	}

	protected function processInsertData(){
		$existed = $this->subModel->where($this->subKey, $this->getMainModelKeyVal())->count();
		if($existed > 0){
			throw new \AppException('Child of object is existed!');
		}
		
		$data = func_get_args()[0];
		if(is_array($data) && array_key_exists(0, $data)){
			throw new \AppException('Cannot insert multiple records in Has One Relation!');
		}

		if(is_object($data) && is_a($data, 'System\Model\Model', true)){
			$data = $data->toArray();
		}


		$data[$this->subKey] = $this->getMainModelKeyVal();
		return $data;
	}
}



 ?>
