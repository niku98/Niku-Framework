<?php
namespace system\database\sqls\builders;
/**
 * QueryBuilder for Database
 */
abstract class QueryBuilder
{
	//Query Builder Properties
	protected $sql = '';
	protected $distinct = false;
	protected $update = '';
	protected $insert = '';
	protected $insertMany = '';
	protected $delete = '';
	protected $select = '';

	protected $join = '';

	protected $where = '';
	protected $on = '';
	protected $having = '';
	protected $orderBy = '';
	protected $groupBy = '';
	protected $limit = '';
	protected $offset = '';

	protected $table;

	protected $operators = [];

	protected function resetBuilder(){
		$this->select = '';
		$this->join = '';
		$this->on = '';
		$this->where = '';
		$this->groupBy = '';
		$this->having = '';
		$this->orderBy = '';
		$this->limit = '';
		$this->offset = '';
		$this->delete = '';
		$this->update = '';
		$this->insert = '';
		$this->insertMany = '';
	}

	/*----------------------------------------
	METHODS TO BUILD QUERY
	----------------------------------------*/
	public final function table($tableName){
		$this->table = $tableName;
		return $this;
	}

	abstract public function addInsertClause(array $cols);
	abstract public function addUpdateClause(array $cols);
	abstract public function addDeleteClause();
	abstract public function addSelectClause(array $cols);

	abstract public function addJoinClause(string $type, array $data);

	abstract public function addWhereClause();
	abstract public function addRawWhere(string $rel, string $raw);
	abstract public function addOnClause();
	abstract public function addRawOn(string $rel, string $raw);
	abstract public function addHavingClause();
	abstract public function addRawHaving(string $rel, string $raw);

	abstract public function addGroupByClause(array $data);
	abstract public function addOrderClause(array $data);
	abstract public function addLimitClause();
	abstract public function addOffsetClause();

	abstract public function isSelect();
	abstract public function distinct();

	/*----------------------------------------
	GET COMPLETE QUERY METHODS
	----------------------------------------*/
	abstract public function getInsertQuery();
	abstract public function getUpdateQuery();
	abstract public function getDeleteQuery();
	abstract public function getSelectQuery();
}


 ?>
