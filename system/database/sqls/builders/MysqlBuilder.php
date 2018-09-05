<?php
namespace system\database\sqls\builders;
use system\database\sqls\builders\QueryBuilder;
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
		$sql = trim($this->select.$this->join.$this->on.$this->where.$this->groupBy.$this->having.$this->orderBy.$this->limit.$this->offset);
		// echo $sql; exit;
		$this->resetBuilder();
		return $sql;
	}

	/*----------------------------------------
	BUILDER METHODS
	----------------------------------------*/

	public function addInsertClause(array $cols){
		$this->insert = 'INSERT INTO `'.$this->table.'` ';
		$this->insert .= '( '.implode($cols, ', ').' )';
		$this->insert .= ' VALUES ';
		$this->insert .= '( '. str_repeat('?, ', count($cols) - 1) .'? )';

		return $this;
	}

	public function addInsertManyClause(array $cols, int $num_values){
		$this->insertMany = 'INSERT INTO `'.$this->table.'` ';
		$this->insertMany .= '( '.implode($cols, ', ').' )';
		$this->insertMany .= ' VALUES ';
		$value = '( '. str_repeat('?, ', count($cols) - 1) .'? )';

		$this->insertMany .= str_repeat($value.', ', $num_values - 1).$value;

		return $this;
	}

	public function addUpdateClause(array $cols){
		$this->update = 'UPDATE `'.$this->table.'` ';
		$this->update .= 'SET '.implode($cols, ' = ?, ').' = ? ';
		return $this;
	}

	public function addDeleteClause(){
		$this->delete = 'DELETE FROM `'.$this->table.'` ';
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
		$this->select .= implode($cols, ', ').' FROM `'.$this->table.'` ';
		return $this;
	}

	public function isSelect(){
		return !empty($this->select);
	}

	public function addJoinClause(string $type, array $data){
		$this->join .= $type.' JOIN ';
		$this->join .= implode($data, ', ').' ';

		return $this;
	}

	public function addWhereClause(){
		if (func_num_args() >= 3 && func_num_args() <= 5) {
			if(!in_array(strtoupper(func_get_args()[2]), $this->operators)){
				throw new Exception("Operator ".func_get_args()[1]." is not suppoted!", 1);
				die();
			}
		}

		$data = func_get_args();

		if(strpos($this->where, 'WHERE') === false){
			$this->where = 'WHERE ';
		}else{
			$this->where .= $data[0].' ';
		}
		if(strpos($data[2], 'BETWEEN') != false){
			$this->where .= $data[1].' '.strtoupper($data[2]).' ? AND ? ';
		}elseif(strpos(strtoupper($data[2]), 'IN') !== false){
			$this->where .= $data[1].' '.strtoupper($data[2]).' ( '.str_repeat('?, ', count($data[3]) - 1).'? ) ';
		}else{
			$this->where .= $data[1].' '.strtoupper($data[2]).' ? ';
		}

		return $this;
	}

	public function addRawWhere(string $rel, string $raw){
		if(strpos($this->where, 'WHERE') === false){
			$this->where = 'WHERE ';
		}else{
			$this->where .= $rel.' ';
		}

		$this->where .= $raw.' ';

		return $this;
	}

	public function addOnClause(){
		if (func_num_args() >= 3 && func_num_args() <= 5) {
			if(!in_array(strtoupper(func_get_args()[2]), $this->operators)){
				throw new Exception("Operator ".func_get_args()[1]." is not suppoted!", 1);
				die();
			}
		}

		$data = func_get_args();

		if(strpos($this->on, 'ON') === false){
			$this->on = 'ON ';
		}else{
			$this->on .= $data[0].' ';
		}

		if(strpos($data[2], 'BETWEEN') != false){
			$this->on .= $data[1].' '.strtoupper($data[2]).' ? AND ? ';
		}elseif(strpos($data[2], 'IN') != false){
			$this->on .= $data[1].' '.strtoupper($data[2]).' ( '.str_repeat('?, ', count($data[3]) - 1).'? ) ';
		}else{
			$this->on .= $data[1].' '.strtoupper($data[2]).' ? ';
		}

		return $this;
	}

	public function addRawOn(string $rel, string $raw){
		if(strpos($this->on, 'ON') === false){
			$this->on = 'ON ';
		}else{
			$this->on .= $rel.' ';
		}

		$this->on .= $raw.' ';

		return $this;
	}

	public function addHavingClause(){
		if (func_num_args() >= 3 && func_num_args() <= 5) {
			if(!in_array(strtoupper(func_get_args()[2]), $this->operators)){
				throw new Exception("Operator ".func_get_args()[1]." is not suppoted!", 1);
				die();
			}
		}

		$data = func_get_args();

		if(strpos($this->having, 'HAVING') === false){
			$this->having = 'HAVING ';
		}else{
			$this->having .= $data[0].' ';
		}

		if(strpos($data[2], 'BETWEEN') != false){
			$this->having .= $data[1].' '.strtoupper($data[2]).' ? AND ? ';
		}elseif(strpos($data[2], 'IN') != false){
			$this->having .= $data[1].' '.strtoupper($data[2]).' ( '.str_repeat('?, ', count($data[3]) - 1).'? ) ';
		}else{
			$this->having .= $data[1].' '.strtoupper($data[2]).' ? ';
		}
		return $this;
	}

	public function addRawHaving(string $rel, string $raw){
		if(strpos($this->having, 'HAVING') === false){
			$this->having = 'HAVING ';
		}else{
			$this->having .= $rel.' ';
		}

		$this->having .= $raw.' ';

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
