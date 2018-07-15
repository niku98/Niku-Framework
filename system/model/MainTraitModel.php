<?php
namespace system\model;
use system\app\AppException;
use system\database\Database;
use system\supporters\Request;

/**
 *
 */
trait MainTraitModel
{
	protected $data = array();
	protected $identification = 'id';
	protected $table;
	protected static $db;

	protected $properties = [];
	protected $hiddens = [];

	/*----------------------------------------
	MAGIC METHODS
	----------------------------------------*/

	public function __construct(array $data = array()){
		if(empty(self::$db)){
			self::$db = Database::table($this->table);
		}
		$this->data = $data;
	}

	public function __set(string $name, $value){
		if(!in_array($name, $this->hiddens))
			$this->data[$name] = $value;
	}

	public function __get(string $name){
		if(!in_array($name, $this->hiddens))
			if(!empty($this->data[$name]))
			 	return $this->data[$name];

			throw new AppException($name.' is undefined!');
	}

	public function __debugInfo(){
		$debug = $this->data;

		if(!empty($this->hiddens)){
			foreach ($this->hiddens as $hidden){
				unset($debug[$hidden]);
			}
		}

		return $debug;
	}

	/*----------------------------------------
	FINAL METHODS
	----------------------------------------*/

	public static function all(string $typeResult = 'object'){
		$called = get_called_class();
		$object = new $called();

		$list = $object->get($typeResult);

		return $list;
	}

	public function insert(){
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->insert(...func_get_args());
			return $object;
		}

		self::changeTable();

		self::$db->insert(...func_get_args());

		return $this;
	}

	public function count(){
		$bt = debug_backtrace();
 		if($bt[0]['type']=='::'){
			$called = get_called_class();
			$object = new $called();
			$object->count();
			return $object;
		}

		self::changeTable();

		return self::$db->count();
	}

	public static function find($value){
		$called = get_called_class();
		$identification = get_class_vars($called)['identification'];

		$object = new $called();

		$data = $object->where($identification, $value)->getFirst();
		return $data;
	}

	public static function findBy(string $key, $value)
	{
		$called = get_called_class();
		$properties = get_class_vars($called)['properties'];

		if(!in_array($key, $properties)){
			throw new AppException("Column $key is not $called's property!");
		}

		$object = new $called();

		$data = $object->where($key, $value)->getFirst('array');
		if(empty($data))
			return false;
		unset($object);
		$object = new $called($data);
		return $object;
	}

	public function get($typeResult = 'object'){
		$this->changeTable();

		$list_array = self::$db->get();
		$called = get_called_class();

		if($typeResult === 'object'){
			$list_object = [];
			foreach ($list_array as $array) {
				$list_object[] = new $called($array);
			}
		}
		return $typeResult == 'object' ? $list_object : $list_array;
	}
	public function getFirst($typeResult = 'object'){
		$called = get_called_class();
		$this->changeTable();

		$result = self::$db->getFirst();
		if(!$result)
			return null;

		return $typeResult === 'object' ? new $called($result) : $result;
	}

	public function delete(){
		$identification = $this->identification;
		$this->changeTable();

		if(!empty($this->data[$identification])){
			return self::$db->where($identification, $this->$identification)->delete();
		}

		return self::$db->delete();
	}

	public function save(){
		$called = get_called_class();
		$this->changeTable();

		$identification = get_class_vars($called)['identification'];

		if(!empty($this->data[$identification])){
			self::$db->where($identification, $this->$identification)->update($this->data);
			return self::$db->affected_rows() != 0 ? true : false;
		}else{
			self::$db->insert($this->data);
			$this->$identification = self::$db->insert_id();
			return self::$db->insert_id() != 0 ? true : false;
		}

		return false;
	}

	public function pagination(int $per_page)
	{
		$request = new Request();
		$page = $request->has('page') ? $request->get('page') : 1;
		$offset = ($page - 1) * $per_page;

		self::changeTable();
		$db = clone self::$db;

		$list_array = $db->limit($per_page)->offset($offset)->get();
		$called = get_called_class();
		$items = array();

		foreach ($list_array as $array) {
			$items[] = new $called($array);
		}

		return self::$db->pagination($per_page, $items);
	}

	public function toArray()
	{
		return $this->data;
	}

	public function toJson(){
		return json_encode($this->data);
	}


	/*----------------------------------
	Model Relationship
	----------------------------------*/

	/*
	* Params: [
	*	model => Target model to get
	*	foreign_key => Name of column in target model point to local_key
	*]
	*
	*/
	public function hasOne(string $model, string $foreign_key = '', string $local_key = ''){
		$foreign_key = empty($foreign_key) ? strtolower(explode('\\', $model)[1]).'_id' : $foreign_key;
		$local_key = empty($local_key) ? (empty($this->identification) ? 'id' : $this->identification) : $local_key;

		$model = new $model();
		return $model->where($foreign_key, $this->$local_key)->getFirst();
	}

	/*
	* Params: [
	*	model => Target model to get
	*	foreign_key => Name of column in target model point to local_key
	*]
	*
	*/
	public function hasMany(string $model, string $foreign_key = '', string $local_key = ''){
		$foreign_key = empty($foreign_key) ? strtolower(explode('\\', $model)[1]).'_id' : $foreign_key;
		$local_key = empty($local_key) ? (empty($this->identification) ? 'id' : $this->identification) : $local_key;

		$model = new $model();
		return $model->where($foreign_key, $this->$local_key)->get();
	}

	/*
	* Params: [
	*	model => Target model to get
	*	foreign_key => Name of column in current model point to local_key of target model
	*]
	*
	*/
	public function belongsTo(string $model, string $foreign_key = '', string $local_key = ''){
		$foreign_key = empty($foreign_key) ? strtolower(explode('\\', get_called_class())[1]).'_id' : $foreign_key;
		$local_key = empty($local_key) ? 'id' : $local_key;

		$model = new $model();
		return $model->where($local_key, $this->$foreign_key)->getFirst();
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
		$targetClassName = strtolower(explode('\\', $model)[1]);
		$thisClassName = strtolower(explode('\\', get_called_class())[1]);

		$tableMid = empty($tableMid) ? $thisClassName.'_'.$targetClassName : $tableMid;
		$this_foreign_key = empty($this_foreign_key) ? strtolower(explode('\\', get_called_class())[1]).'_id' : $this_foreign_key;
		$target_foreign_key = empty($target_foreign_key) ? $targetClassName.'_id' : $target_foreign_key;

		$identification = $this->identification;

		$list_key = self::$db->table($tableMid)->distince()->select($target_foreign_key)->where($this_foreign_key, $this->$identification)->get();
		$list = array();
		foreach ($list_key as $key) {
			$list[] = $key[$target_foreign_key];
		}
		$model = new $model();
		return $model->where('id', 'IN', $list)->get();
	}
}

 ?>
