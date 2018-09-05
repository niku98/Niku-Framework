<?php
namespace system\model;
use system\patterns\abstracts\HasDataProperty;
/**
 * Parent Model for another model extends
 */
class Model
{
	use HasRelation;
	use HasDataProperty;

	protected $identification = 'id';
	protected $table;


	protected $properties = [];
	protected $hiddens = [];

	/*----------------------------------------
	MAGIC METHODS
	----------------------------------------*/

	public function __construct(array $data = array()){
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

	public function __call($method, $args)
	{
		return $this->newBuilder()->$method(...$args);
	}

	public static function __callStatic($method, $args)
	{
		return (new static)->$method(...$args);
	}

	/*----------------------------------------------*/

	public function getTable()
	{
		return $this->table;
	}

	public function newBuilder()
	{
		return new Builder($this);
	}

	public function newInstance(array $data = [])
	{
		return new static($data);
	}

	/*----------------------------------------
	FINAL METHODS
	----------------------------------------*/

	public static function all(){
		$object = new static();

		$list = $object->get();

		return $list;
	}

	public static function find($value){
		$object = new static();

		$data = $object->where($object->identification, $value)->first();
		return $data;
	}

	public static function findBy(string $key, $value)
	{
		$called = get_called_class();
		$properties = get_class_vars($called)['properties'];

		if(!in_array($key, $properties)){
			throw new AppException("Column $key is not $called's property!");
		}

		$object = new static();

		$data = $object->where($key, $value)->first()->toArray();
		if(empty($data))
			return false;

		unset($object);
		$object = new static($data);
		return $object;
	}

	public function delete(){
		$identification = $this->identification;


		if(!empty($this->data[$identification])){
			return $this->where($identification, $this->$identification)->delete();
		}

		return $this->newBuilder()->delete();
	}

	public function save(){


		$identification = $this->identification;

		if(!empty($this->data[$identification])){
			$affected_rows = $this->where($identification, $this->$identification)->update($this->data)->affected_rows();
			return $affected_rows != 0 ? true : false;
		}else{
			$this->insert($this->data);
			$this->$identification = $this->insert_id();
			return $this->db->insert_id() != 0 ? true : false;
		}

		return false;
	}

	public function toArray()
	{
		return $this->data;
	}

	public function toJson(){
		return json_encode($this->data);
	}
}


 ?>
