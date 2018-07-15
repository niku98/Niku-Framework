<?php

namespace system\database\sqls;
use system\database\sqls\connections\ConnectionFactory;
use system\database\sqls\builders\QueryBuilderFactory;
use system\database\DatabaseInterface;
use system\app\AppException;
use system\supporters\Paginator;
use system\supporters\Request;


class Database implements DatabaseInterface
{
	protected $connection;
	protected $database;
	private $connector;
	private $builder;
	private $sql;

	private static $rawSql = false;

	protected $table;


	/*
	* Constructor
	*/
	function __construct($tableName = '')
	{
		global $_CONFIG;

		$driver = $this->connection ?? $_CONFIG['DB_CONNECTION'];
		$database = $this->database ?? $_CONFIG['DB_DATABASE'];

		$this->connector = ConnectionFactory::create($driver, $database);
		$this->builder = QueryBuilderFactory::create($driver);

		$this->table = $tableName;

		$this->builder->table($this->table);
	}

	public function close(){
		return $this->connector->close();
	}

	public function changeTable($tableName){
		$this->builder->table($tableName);
		return $this;
	}

	/*----------------------------------------
	QUERY BUILDER FUNCTIONS
	----------------------------------------*/

	public final static function table($name){
		$called = get_called_class();
		return new $called($name);
	}

	public final static function raw(string $sql){
		self::$rawSql = true;
		return $sql;
	}

	public final function distinct(){
		$this->builder->distinct();
		return $this;
	}

	public final function select($data = ['*']){
		$data = is_array($data) ? $data : func_get_args();

		$this->builder->addSelectClause($data);

		return $this;
	}

	public function join($tables){
		$tables = is_array($tables) ? $tables : func_get_args();

		$this->builder->addJoinClause('INNER', $tables);

		return $this;
	}

	public function leftJoin($tables){
		$tables = is_array($tables) ? $tables : func_get_args();

		$this->builder->addJoinClause('LEFT', $tables);

		return $this;
	}

	public function rightJoin($tables){
		$tables = is_array($tables) ? $tables : func_get_args();

		$this->builder->addJoinClause('RIGHT', $tables);

		return $this;
	}

	public function where(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawWhere('', func_get_args()[0]);
				self::$rawSql = false;

				return $this;
			}
		}
		elseif(func_num_args() == 2)
			$this->builder->addWhereClause('', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addWhereClause('', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method where()", 1);
			die();
		}

		$this->connector->logicParamsProcess('where', ...func_get_args());

		return $this;
	}

	public function andWhere(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawWhere('AND', func_get_args()[0]);
				self::$rawSql = false;

				return $this;
			}
		}
		elseif(func_num_args() == 2)
			$this->builder->addWhereClause('AND', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addWhereClause('AND', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method andWhere()", 1);
			die();
		}

		$this->connector->logicParamsProcess('where', ...func_get_args());

		return $this;
	}

	public function orWhere(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawWhere('OR', func_get_args()[0]);
				self::$rawSql = false;

				return $this;
			}
		}
		elseif(func_num_args() == 2)
			$this->builder->addWhereClause('OR', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addWhereClause('OR', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method orWhere()", 1);
			die();
		}

		$this->connector->logicParamsProcess('where', ...func_get_args());

		return $this;
	}

	public function on(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawOn('', func_get_args()[0]);
				self::$rawSql = false;

				return $this;
			}
		}
		elseif(func_num_args() == 2)
			$this->builder->addOnClause('', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addOnClause('', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method on()", 1);
			die();
		}

		$this->connector->logicParamsProcess('on', ...func_get_args());

		return $this;
	}

	public function andOn(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawOn('AND', func_get_args()[0]);
				self::$rawSql = false;

				return $this;
			}
		}
		elseif(func_num_args() == 2)
			$this->builder->addOnClause('AND', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addOnClause('AND', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method andOn()", 1);
			die();
		}

		$this->connector->logicParamsProcess('on', ...func_get_args());

		return $this;
	}

	public function orOn(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawOn('OR', func_get_args()[0]);
				self::$rawSql = false;

				return $this;
			}
		}
		elseif(func_num_args() == 2)
			$this->builder->addOnClause('OR', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addOnClause('OR', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method orOn()", 1);
			die();
		}

		$this->connector->logicParamsProcess('on', ...func_get_args());

		return $this;
	}

	public function having(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawHaving('', func_get_args()[0]);
				self::$rawSql = false;

				return $this;
			}
		}
		elseif(func_num_args() == 2)
			$this->builder->addHavingClause('', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addHavingClause('', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method having()", 1);
			die();
		}

		$this->connector->logicParamsProcess('having', ...func_get_args());

		return $this;
	}

	public function andHaving(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawHaving('AND', func_get_args()[0]);
				self::$rawSql = false;

				return $this;
			}
		}
		elseif(func_num_args() == 2)
			$this->builder->addHavingClause('AND', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addHavingClause('AND', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method andHaving()", 1);
			die();
		}

		$this->connector->logicParamsProcess('having', ...func_get_args());

		return $this;
	}

	public function orHaving(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawHaving('OR', func_get_args()[0]);
				self::$rawSql = false;

				return $this;
			}
		}
		elseif(func_num_args() == 2)
			$this->builder->addHavingClause('OR', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addHavingClause('OR', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method orHaving()", 1);
			die();
		}

		$this->connector->logicParamsProcess('having', ...func_get_args());

		return $this;
	}

	public function groupBy($data){
		$data = is_array($data) ? $data : func_get_args();

		if(count($data) < 1){
			throw new AppException("Number of parameters is not valid for method groupBy()", 1);
			die();
		}

		$this->builder->addGroupByClause($data);

		return $this;
	}

	public function orderBy($data){
		$data = is_array($data) ? $data : func_get_args();

		if(count($data) < 1){
			throw new AppException("Number of parameters is not valid for method orderBy()", 1);
			die();
		}

		$this->builder->addOrderClause($data);

		$this->connector->addOrderParams($data);

		return $this;
	}

	public function limit(int $data){
		$this->builder->addLimitClause();

		$this->connector->addLimitParams($data);

		return $this;
	}

	public function offset(int $data){
		$this->builder->addOffsetClause();

		$this->connector->addOffsetParams($data);

		return $this;
	}

	/*----------------------------------------
	RESULT FUNCTIONS
	----------------------------------------*/

	public function insert(array $data){
		if(!empty($data[0]) && is_array($data[0])){
			return $this->insertMany($data);
		}else{
			return $this->insertOne($data);
		}
	}

	public final function insertOne(array $data){
		$this->builder->addInsertClause(array_keys($data));
		$this->connector->addInsertParams(array_values($data));

		$this->connector->exec($this->builder->getInsertQuery());

		return $this;
	}

	public final function insertMany(array $data){
		$keys = array_keys($data[0]);
		sort($keys);
		$this->builder->addInsertManyClause($keys, count($data));
		$this->connector->addInsertManyParams(array_values($data));

		$this->connector->exec($this->builder->getInsertManyQuery());

		return $this;
	}

	public final function update(array $data){
		$this->builder->addUpdateClause(array_keys($data));
		$this->connector->addUpdateParams(array_values($data));

		$this->connector->exec($this->builder->getUpdateQuery());
		return $this;
	}

	public function delete(){
		$this->builder->addDeleteClause();

		$this->connector->exec($this->builder->getDeleteQuery());
		return $this;
	}

	public final function updateOrInsert(array $data){
		if($this->count()){
			return $this->update($data);
		}else{
			return $this->insert($data);
		}
	}

	// Get Query Result
	// Return data: Array of result
	public function get(){

		$this->connector->exec($this->builder->getSelectQuery())->get_result();
		$result = array();
		while ($row = $this->connector->fetch()) {
			array_push($result, $row);
		}
		return $result;
	}

	// Get first result of query result
	// Return First result value
	public function getFirst(){
		$this->connector->exec($this->builder->getSelectQuery())->get_result();
		return $this->connector->fetch();
	}

	// Get number of rows result
	// Return integer - num_rows
	public function count(){
		$this->connector->exec($this->builder->getSelectQuery())->get_result();
		return $this->connector->num_rows;
	}

	// Get affected row
	public function affected_rows(){
		return $this->connector->affected_rows;
	}

	// Get row id(primary key) inserted row
	public function insert_id(){
		return $this->connector->insert_id;
	}

	// Fetcth query result
	// @param{type => 'Fetch type'}
	public function fetch(string $type = 'assoc'){
		return $this->connector->fetch($type);
	}

	public function pagination(int $per_page, array $items = [])
	{
		$old_builder = clone $this->builder;
		$old_connector = clone $this->connector;

		$total_items = $old_connector->exec($old_builder->getSelectQuery())->get_result()->num_rows;
		if(empty($items) || !count($items)){
			$request = new Request();
			$page = $request->has('page') ? $request->get('page') : 1;
			$offset = ($page - 1) * $per_page;

			$items = $this->limit($per_page)->offset($offset)->get();
		}

		return new Paginator($per_page, $total_items, $items);
	}
}

 ?>
