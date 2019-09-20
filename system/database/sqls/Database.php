<?php

namespace System\Database\Sqls;
use System\Database\Sqls\connections\ConnectionFactory;
use System\Database\Sqls\builders\QueryBuilderFactory;
use System\Database\DatabaseInterface;
use \AppException;
use System\Supporters\Paginator;
use System\Supporters\Collection;
use Request;


class Database implements DatabaseInterface
{
	protected $connection;
	protected $database;
	private $connector;
	private $builder;
	private $sql;

	private static $queryLogStatus = false;
	private static $queryLogs = [];

	private static $rawSql = false;

	protected $table;


	/*
	* Constructor
	*/
	function __construct($tableName = '')
	{
		$driver = $this->connection ?? app()->env('DB_CONNECTION');
		$database = $this->database ?? app()->env('DB_DATABASE');

		$this->connector = ConnectionFactory::create($driver, $database);
		$this->builder = QueryBuilderFactory::create($driver);

		$this->table = $tableName;

		$this->builder->table($this->table);
	}

	public function __clone()
	{
		$this->connector = clone $this->connector;
		$this->builder = clone $this->builder;
	}

	public function close(){
		return $this->connector->close();
	}

	public function changeTable($tableName){
		$this->builder->table($tableName);
		return $this;
	}

	public final function query($sql)
	{
		$this->connector->exec($sql);
		return $this;
	}

	public static function queryLogOn()
	{
		self::$queryLogStatus = true;
	}

	public static function queryLogOff()
	{
		self::$queryLogStatus = false;
	}

	public static function getQueryLogs()
	{
		return self::$queryLogs;
	}

	/*----------------------------------------
	QUERY BUILDER FUNCTIONS
	----------------------------------------*/

	public final function table($name){
		return $this->changeTable($name);
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

	public function join($table, $column1 = NULL, $operator = NULL, $column2 = NULL){
		$this->builder->addJoinClause('INNER', $table, $column1, $operator, $column2);

		return $this;
	}

	public function leftJoin($table, $column1 = NULL, $operator = NULL, $column2 = NULL){
		$this->builder->addJoinClause('LEFT', $table, $column1, $operator, $column2);

		return $this;
	}

	public function rightJoin($tables, $column1 = NULL, $operator = NULL, $column2 = NULL){
		$this->builder->addJoinClause('RIGHT', $table, $column1, $operator, $column2);

		return $this;
	}

	public function where(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawLogicClause('where', 'AND', func_get_args()[0]);
				self::$rawSql = false;

			}else if(is_callable(func_get_args()[0])){
				$this->builder->groupLogicStart('where');
				$callable = func_get_args()[0];
				$callable($this);
				$this->builder->groupEnd('where');
			}
			return $this;
		}
		elseif(func_num_args() == 2)
			$this->builder->addLogicClause('where', 'AND', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addLogicClause('where', 'AND', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method where()", 1);
		}

		$this->connector->logicParamsProcess('where', ...func_get_args());

		return $this;
	}

	public function orWhere(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawLogicClause('where', 'OR', func_get_args()[0]);
				self::$rawSql = false;

			}else if(is_callable(func_get_args()[0])){
				$this->builder->groupLogicStart('where');
				$callable = func_get_args()[0];
				$callable($this);
				$this->builder->groupEnd('where');
			}
			return $this;
		}
		elseif(func_num_args() == 2)
			$this->builder->addLogicClause('where', 'OR', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addLogicClause('where', 'OR', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method orWhere()", 1);
		}

		$this->connector->logicParamsProcess('where', ...func_get_args());

		return $this;
	}

	public function having(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawLogicClause('having', 'AND', func_get_args()[0]);
				self::$rawSql = false;

			}else if(is_callable(func_get_args()[0])){
				$this->builder->groupLogicStart('having');
				$callable = func_get_args()[0];
				$callable($this);
				$this->builder->groupEnd('having');
			}
			return $this;
		}
		elseif(func_num_args() == 2)
			$this->builder->addLogicClause('having', 'AND', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addLogicClause('having', 'AND', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method having()", 1);
		}

		$this->connector->logicParamsProcess('having', ...func_get_args());

		return $this;
	}

	public function orHaving(){
		if(func_num_args() == 1){
			if(self::$rawSql === true){
				$this->builder->addRawLogicClause('having', 'OR', func_get_args()[0]);
				self::$rawSql = false;

			}else if(is_callable(func_get_args()[0])){
				$this->builder->groupLogicStart('having');
				$callable = func_get_args()[0];
				$callable($this);
				$this->builder->groupEnd('having');
			}
			return $this;
		}
		elseif(func_num_args() == 2)
			$this->builder->addLogicClause('having', 'OR', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->builder->addLogicClause('having', 'OR', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method orHaving()", 1);
		}

		$this->connector->logicParamsProcess('having', ...func_get_args());

		return $this;
	}

	public function groupBy($data){
		$data = is_array($data) ? $data : func_get_args();

		if(count($data) < 1){
			throw new AppException("Number of parameters is not valid for method groupBy()", 1);
		}

		$this->builder->addGroupByClause($data);

		return $this;
	}

	public function orderBy($data){
		$data = is_array($data) ? $data : func_get_args();

		if(count($data) < 1){
			throw new AppException("Number of parameters is not valid for method orderBy()", 1);
		}

		$this->builder->addOrderClause($data);

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
		$sql = $this->builder->addInsertClause(array_keys($data))->getInsertQuery();
		$bindParams = $this->connector->addInsertParams(array_values($data))->bindParams()->getBindParams();
		if(self::$queryLogStatus){
			self::$queryLogs[] = array(
				'sql' => $sql,
				'bind_params' => $bindParams
			);
		}

		$this->connector->exec($sql);

		return $this;
	}

	public final function insertMany(array $data){
		$keys = array_keys($data[0]);
		sort($keys);
		$sql = $this->builder->addInsertManyClause($keys, count($data))->getInsertManyQuery();
		$bindParams = $this->connector->addInsertManyParams(array_values($data))->bindParams()->getBindParams();

		if(self::$queryLogStatus){
			self::$queryLogs[] = array(
				'sql' => $sql,
				'bind_params' => $bindParams
			);
		}

		$this->connector->exec($sql);

		return $this;
	}

	public final function update(array $data){
		$sql = $this->builder->addUpdateClause(array_keys($data))->getUpdateQuery();
		$bindParams = $this->connector->addUpdateParams(array_values($data))->bindParams()->getBindParams();

		if(self::$queryLogStatus){
			self::$queryLogs[] = array(
				'sql' => $sql,
				'bind_params' => $bindParams
			);
		}

		$this->connector->exec($sql);
		return $this;
	}

	public function delete(){
		$sql = $this->builder->addDeleteClause()->getDeleteQuery();
		$bindParams = $this->connector->bindParams()->getBindParams();

		if(self::$queryLogStatus){
			self::$queryLogs[] = array(
				'sql' => $sql,
				'bind_params' => $bindParams
			);
		}

		$this->connector->exec($sql);
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
		$sql = $this->builder->getSelectQuery();
		$bindParams = $this->connector->bindParams()->getBindParams();

		if(self::$queryLogStatus){
			self::$queryLogs[] = array(
				'sql' => $sql,
				'bind_params' => $bindParams
			);
		}

		$this->connector->exec($sql)->get_result();
		$result = array();
		while ($row = $this->connector->fetch()) {
			array_push($result, $row);
		}
		return new Collection($result);
	}

	// Get first result of query result
	// Return First result value
	public function first(){
		$sql = $this->builder->getSelectQuery();
		$bindParams = $this->connector->bindParams()->getBindParams();

		if(self::$queryLogStatus){
			self::$queryLogs[] = array(
				'sql' => $sql,
				'bind_params' => $bindParams
			);
		}

		$this->connector->exec($sql)->get_result();
		return $this->connector->fetch();
	}

	// Get number of rows result
	// Return integer - num_rows
	public function count(){
		if($this->connector->num_rows == -1){
			$bindParams = $this->connector->bindParams()->getBindParams();
			$sql = $this->builder->getSelectQuery();

			if(self::$queryLogStatus){
				self::$queryLogs[] = array(
					'sql' => $sql,
					'bind_params' => $bindParams
				);
			}

			$this->connector->exec($sql)->get_result();
		}
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

	public function paginate(int $per_page, array $items = NULL)
	{
		if(!is_array($items)){
			$old_builder = clone $this->builder;
			$old_connector = clone $this->connector;

			$total_items = $old_connector->exec($old_builder->getSelectQuery())->get_result()->num_rows;

			$request = Request::getInstance();
			$page = $request->has('page') ? $request->get('page') : 1;
			$offset = ($page - 1) * $per_page;

			$items = $this->limit($per_page)->offset($offset)->get();
		}else{
			$total_items = $this->count();
		}
		return (new Paginator($per_page, $total_items, $items));
	}
}

 ?>
