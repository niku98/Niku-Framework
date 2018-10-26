<?php
namespace System\database\Sqls\builders;
use System\database\Sqls\builders\QueryBuilder;
use AppException;
/**
* Mysql Query Builder
*/
class MysqlBuilder extends QueryBuilder
{

	protected $operators = [
		'=', '<', '>', '<=', '>=', '<=>', '!=', '<>',
		'IS', 'IS NOT', 'LIKE', 'NOT LIKE','BETWEEN', 'NOT BETWEEN', 'IN',
		'NOT IN'
	];

	/*----------------------------------------
	GET COMPLETE QUERY METHODS
	----------------------------------------*/

	public function getInsertQuery(){
		$sql = trim($this->insert);
		$this->resetBuilder();
		return $sql;
	}

	public function getInsertManyQuery(){
		$sql = trim($this->insertMany);
		$this->resetBuilder();
		return $sql;
	}

	public function getUpdateQuery(){
		$sql = trim($this->update.$this->where);
		$this->resetBuilder();
		return $sql;
	}

	public function getDeleteQuery(){
		$sql = trim($this->delete.$this->where);
		$this->resetBuilder();
		return $sql;
	}

	public function getSelectQuery(){
		if(!$this->isSelect())
			$this->addSelectClause(['*']);
		$sql = trim($this->select.implode($this->joins, ' ').' '.$this->where.$this->groupBy.$this->having.$this->orderBy.$this->limit.$this->offset);
		$this->resetBuilder();
		return $sql;
	}

	/*----------------------------------------
	BUILDER METHODS
	----------------------------------------*/

	public function addInsertClause(array $cols){
		$this->insert = 'INSERT INTO '.$this->table.' ';
		$this->insert .= '( '.implode($cols, ', ').' )';
		$this->insert .= ' VALUES ';
		$this->insert .= '( '. str_repeat('?, ', count($cols) - 1) .'? )';

		return $this;
	}

	public function addInsertManyClause(array $cols, int $num_values){
		$this->insertMany = 'INSERT INTO '.$this->table.' ';
		$this->insertMany .= '( '.implode($cols, ', ').' )';
		$this->insertMany .= ' VALUES ';
		$value = '( '. str_repeat('?, ', count($cols) - 1) .'? )';

		$this->insertMany .= str_repeat($value.', ', $num_values - 1).$value;

		return $this;
	}

	public function addUpdateClause(array $cols){
		$this->update = 'UPDATE '.$this->table.' ';
		$this->update .= 'SET '.implode($cols, ' = ?, ').' = ? ';
		return $this;
	}

	public function addDeleteClause(){
		$this->delete = 'DELETE FROM '.$this->table.' ';
		return $this;
	}

	public function distinct(){
		$this->distinct = true;
		return $this;
	}

	public function addSelectClause(array $cols){
		$this->select = 'SELECT ';
		if($this->distinct){
			$this->select .= 'DISTINCT ';
		}
		$this->select .= implode($cols, ', ').' FROM '.$this->table.' ';
		return $this;
	}

	public function isSelect(){
		return !empty($this->select);
	}

	public function addJoinClause(string $type, $table, $column1 = NULL, $operator = NULL, $column2 = NULL){
		$join = new JoinBuilder($type, $table);
		if(!is_null($column1)){
			$join->on($column1, $operator, $column2);
		}
		$this->joins[] = $join;
		return $this;
	}

	public function addLogicClause($type, $logic, $column, $operator, $param1, $param2 = NULL)
	{
		$this->$type;
		if(strpos($this->$type, strtoupper($type)) === false){
			$this->$type = strtoupper($type).' ';
		}else if($this->$type[strlen($this->$type) - 2] != '('){
			$this->$type .= $logic.' ';
		}

		if(strpos(strtoupper($operator), 'BETWEEN') !== false){
			$this->$type .= $column.' '.strtoupper($operator).' ? AND ? ';
		}elseif(strpos(strtoupper($operator), 'IN') !== false){
			$this->$type .= $column.' '.strtoupper($operator).' ( '.str_repeat('?, ', count($param1) - 1).'? ) ';
		}else{
			$this->$type .= $column.' '.strtoupper($operator).' ? ';
		}

		return $this;
	}

	public function addRawLogicClause(string $type, string $rel, string $raw){
		if(strpos($this->$type, strtoupper($type)) === false){
			$this->$type = strtoupper($type).' ';
		}else{
			$this->$type .= $rel.' ';
		}

		$this->$type .= $raw.' ';

		return $this;
	}

	public function addGroupByClause(array $data){
		$this->groupBy = 'GROUP BY ';

		$this->groupBy .= implode($data, ', ').' ';

		return $this;
	}

	public function addOrderClause(array $data){
		$this->orderBy = 'ORDER BY ';

		if(!empty($data[0]) && is_string($data[0])){
			$this->orderBy .= '`'.$data[0].'` '.($data[1] ?? 'ASC');
		}else{
			$isFirst = true;
			foreach($data as $col => $dir){
				if($isFirst){
					$this->orderBy .= '`'.$col.'` '.($dir ?? 'ASC');
				}else{
					$this->orderBy .= ', `'.$col.'` '.($dir ?? 'ASC');
				}
				$isFirst = false;
			}
		}

		$this->orderBy .= ' ';
		return $this;
	}

	public function groupLogicStart($clause = NULL, $logic = 'AND')
	{
		if(strpos($this->$clause, strtoupper($clause)) === false){
			$this->$clause = strtoupper($clause).' ';
		}else{
			$this->$clause .= strtoupper($logic).' ';
		}
		$this->$clause .= '( ';
		return $this;
	}

	public function groupEnd($clause = NULL)
	{
		$this->$clause .= ') ';
		return $this;
	}

	public function addLimitClause(){
		$this->limit = 'LIMIT ? ';
		return $this;
	}

	public function addOffsetClause(){
		$this->offset = 'OFFSET ?';
		return $this;
	}

	public function isWhereEmpty(){
		return empty($this->where);
	}
}


?>
