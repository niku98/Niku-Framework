<?php
namespace System\Database\NoSqls;
use System\Database\DatabaseInterface;

/**
 *
 */
class Database implements DatabaseInterface
{
	protected $table;
	protected $connection;
	protected $database;

	function __construct(string $table = '')
	{
		global $_CONFIG;

		$host_address = 'mongodb://'.$_CONFIG['DB_HOST'].':'.$_CONFIG['DB_PORT'];
		$this->connection = new \MongoDB\Client($host_address);

		$database = $this->database ?? $_CONFIG['DB_DATABASE'];
		$this->connection = $this->connection->$database;
		$this->table = $table;
	}

	private function getCollectionConnect(){
		return $this->connection->selectCollection($this->table);
	}

	public static function table($name){
		return new self($name);
	}

	public function changeTable($name){
		$this->table = $name;
		return $this;
	}

	public function insert(array $data){
		$this->getCollectionConnect()->insertOne($data);
	}

	public function update(array $data){

	}

	public function delete(){}

	public function updateOrInsert(array $data){

	}

	public function distinct(){}
	public function select($data = ['*']){}

	public function join($tables){}
	public function leftJoin($tables){}
	public function rightJoin($tables){}

	public function where(){}
	public function andWhere(){}
	public function orWhere(){}
	public function on(){}
	public function andOn(){}
	public function orOn(){}
	public function having(){}
	public function andHaving(){}
	public function orHaving(){}

	public function orderBy($data){}
	public function limit(int $data){}
	public function offset(int $data){}
}


 ?>
