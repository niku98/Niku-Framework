<?php
namespace system\patterns\abstracts;
use AppException;
/**
 *
 */
trait HasDataProperty
{
	protected $data = array();

	public function __debugInfo(){
		return $this->data ?? NULL;
	}

	public function __get($key){
		if(!isset($this->data[$key])){
			if(method_exists($this, $key)){
				return $this->$key();
			}else {
				throw new AppException("Property [$key] not found!");

			}
		}
		return $this->data[$key];
	}

	public function __set($key, $value){
		$this->data[$key] = $value;
	}
}



 ?>
