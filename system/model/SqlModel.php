<?php
namespace system\model;
use system\database\Database;
use system\app\AppException;

trait SqlModel{

	private function changeTable(){
		$called = get_called_class();
		$table = get_class_vars($called)['table'];
		if(!self::$db){
			self::$db = Database::table($table);
		}else
			self::$db->changeTable($table);
	}

	/*----------------------------------------
	BUILDER METHODS
	----------------------------------------*/

	public function select($data = ['*'])
	{
		$data = is_array($data) ? $data : func_get_args();
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->select($data);
			return $object;
		}
		$this->changeTable();
		self::$db->select($data);
		return $this;
	}

	public function join($tables){
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->join($tables);
			return $object;
		}

		self::$db->join($tables);
		return $this;
	}

	public function leftJoin($tables){
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->leftJoin($tables);
			return $object;
		}

		self::$db->leftJoin($tables);
		return $this;
	}

	public function rightJoin($tables){
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->rightJoin($tables);
			return $object;
		}

		self::$db->rightJoin($tables);

		return $this;
	}

	public function where(){
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->where(...func_get_args());
			return $object;
		}
		self::$db->where(...func_get_args());

		return $this;
	}

	public function andWhere(){
		self::$db->andWhere(...func_get_args());

		return $this;
	}

	public function orWhere(){
		self::$db->orWhere(...func_get_args());

		return $this;
	}

	public function on(){
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->on(...func_get_args());
			return $object;
		}

		self::$db->on(...func_get_args());
		return $this;
	}

	public function andOn(){
		self::$db->andOn(...func_get_args());

		return $this;
	}

	public function orOn(){
		self::$db->orOn(...func_get_args());

		return $this;
	}

	public function having(){
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->having(...func_get_args());
			return $object;
		}

		self::$db->having(...func_get_args());

		return $this;
	}

	public function andHaving(){
		$this->andHaving(...func_get_args());

		return $this;
	}

	public function orHaving(){
		self::$db->orHaving(...func_get_args());

		return $this;
	}

	public function groupBy($data){
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->groupBy($data);
			return $object;
		}

		self::$db->groupBy($data);

		return $this;
	}

	public function orderBy($data){
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->orderBy($data);
			return $object;
		}

		self::$db->orderBy($data);

		return $this;
	}

	public function limit(int $data){
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->limit($data);
			return $object;
		}

		self::$db->limit($data);

		return $this;
	}

	public function offset(int $data){
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->offset($data);
			return $object;
		}

		self::$db->offset($data);

		return $this;
	}
}

 ?>
