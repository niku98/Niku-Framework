<?php
namespace System\Model;
use System\Patterns\Abstracts\HasDataProperty;
use System\Patterns\Abstracts\NkArrayAccess;
use AppException;

/**
 * Parent Model for another model extends
 */
class Model extends NkArrayAccess
{
	use HasRelation;
	use HasDataProperty;

	protected $primaryKey = 'id';
	protected $table;


	protected $properties = [];
	protected $hiddens = [];

	/*----------------------------------------
	MAGIC METHODS
	----------------------------------------*/

	public function __construct(array $data = array()){
		$this->data = $data;

		return $this;
	}

	public function __set(string $name, $value){
		if(!in_array($name, $this->hiddens)){
			$this->data[$name] = $value;
		}
	}

	public function __get(string $name){
		if(!in_array($name, $this->hiddens)){
			if(isset($this->data[$name])){
				return $this->data[$name];
			}else if(\method_exists($this, $name)){
				$result = $this->{$name}();
				if($result instanceof \System\Model\Relations\Relation || $result instanceof \System\Model\Relations\HasManyThrough){
					return $result->get();
				}
			}
		}

		throw new AppException($name.' is undefined in Model ['.get_called_class().']!');
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

	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	public function getPrimaryKeyVal()
	{
		return $this->{$this->primaryKey};
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
		$called = get_called_class();
		$primaryKey = get_class_vars($called)['primaryKey'];
		return static::where($primaryKey, $value)->first();
	}

	public static function findBy(string $key, $value)
	{
		$called = get_called_class();
		$properties = get_class_vars($called)['properties'];

		if(!in_array($key, $properties)){
			throw new AppException("Column $key is not $called's property!");
		}

		return static::where($key, $value)->first();
	}

	public function delete(){
		$primaryKey = $this->primaryKey;


		if(!empty($this->data[$primaryKey])){
			return $this->where($primaryKey, $this->$primaryKey)->delete();
		}

		return $this->newBuilder()->delete();
	}

	public function save(){
		$primaryKey = $this->primaryKey;

		if(!empty($this->data[$primaryKey])){
			$affected_rows = $this->where($primaryKey, $this->$primaryKey)->update($this->data)->affected_rows();
			return $affected_rows != 0 ? true : false;
		}else{
			$this->$primaryKey = $this->insert($this->data)->insert_id();
			return $this->$primaryKey != 0 ? true : false;
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
