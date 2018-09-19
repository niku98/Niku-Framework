<?php
namespace system\database\sqls\connections;
use system\database\sqls\connections\SqlConnection;
use mysqli;
use AppException;

/**
 * Mysql Connection
 */
class MysqlConnection extends SqlConnection
{
	public function __construct($database){
		$this->conn = new mysqli(app()->config('DB_HOST').':'.app()->config('DB_PORT'), app()->config('DB_USER'), app()->config('DB_PASSWORD'), $database);
		$this->conn->set_charset('utf8');
	}

	public function exec(string $sql){
		if($this->query = $this->conn->prepare($sql)){
			$this->processBindParams();
			if(count($this->bindParams)){
				$this->query->bind_param(str_repeat('s', count($this->bindParams)), ...$this->bindParams);
			}
			if(!$this->query->execute()){
				throw new AppException($this->query->error);
			}
			$this->affected_rows = $this->query->affected_rows;
			$this->insert_id = $this->query->insert_id;
			$this->resetData();
			return $this;
		}else{
			throw new AppException($this->conn->error);
		}
	}

	public function get_result(){
		if(!empty($this->result))
			return $this;
		$this->result = $this->query->get_result();
		$this->num_rows = $this->result->num_rows;
		return $this;
	}

	public function fetch(string $type = 'assoc'){
		$fetchType = 'fetch_'.$type;
		return $this->result->$fetchType();
	}

	public function close()
	{
		$this->conn->close();
	}

	/*----------------------------------------
	PROCESSING DATA METHODS
	----------------------------------------*/

	protected function processBindParams(){
		$this->bindParams = array();
		if(!empty($this->insertData) && count($this->insertData)){
			array_push($this->bindParams, ...$this->insertData);
			return $this;
		}

		if(!empty($this->insertManyData) && count($this->insertManyData)){
			array_push($this->bindParams, ...$this->insertManyData);
			return $this;
		}

		if(!empty($this->updateData) && count($this->updateData)){
			array_push($this->bindParams, ...$this->updateData);
		}

		if(!empty($this->onData) && count($this->onData)){
			array_push($this->bindParams, ...$this->onData);
		}

		if(!empty($this->whereData) && count($this->whereData)){
			array_push($this->bindParams, ...$this->whereData);
		}

		if(!empty($this->havingData) && count($this->havingData)){
			array_push($this->bindParams, ...$this->havingData);
		}

		if(!empty($this->orderData) && count($this->orderData)){
			array_push($this->bindParams, ...$this->orderData);
		}

		if(!empty($this->limitData) && count($this->limitData)){
			array_push($this->bindParams, ...$this->limitData);
		}

		if(!empty($this->offsetData) && count($this->offsetData)){
			array_push($this->bindParams, ...$this->offsetData);
		}
	}

	public function addInsertParams(array $data){
		if(!empty($this->insertData) && count($this->insertData)){
			array_push($this->insertData, ...$data);
		}
		else {
			$this->insertData = $data;
		}
		return $this;
	}

	public function addInsertManyParams(array $data){
		foreach ($data as $arr) {
			ksort($arr);
			if(!empty($this->insertManyData) && count($this->insertManyData)){
				array_push($this->insertManyData, ...array_values($arr));
			}
			else {
				$this->insertManyData = array_values($arr);
			}
		}

		return $this;
	}

	public function addUpdateParams(array $data){
		if(!empty($this->updateData) && count($this->updateData)){
			array_push($this->updateData, ...$data);
		}
		else {
			$this->updateData = $data;
		}
		return $this;
	}

	public function addOnParams(array $data){
		if(!empty($this->onData) && count($this->onData)){
			array_push($this->onData, ...$data);
		}
		else {
			$this->onData = $data;
		}
		return $this;
	}

	protected function addWhereParams(array $data){
		if(!empty($this->whereData) && count($this->whereData)){
			array_push($this->whereData, ...$data);
		}
		else {
			$this->whereData = $data;
		}
		return $this;
	}

	public function addHavingParams(array $data){
		if(!empty($this->havingData) && count($this->havingData)){
			array_push($this->havingData, ...$data);
		}
		else {
			$this->havingData = $data;
		}
		return $this;
	}

	public function addOrderParams(array $data){
		if(!empty($data[0]) && is_string($data[0])){
			if(!empty($this->orderData) && count($this->orderData)){
				array_push($this->orderData, ...$data);
			}
			else {
				$this->orderData = $data;
			}
			if(count($data) == 1)
				array_push($this->orderData, 'ASC');
		}else{
			foreach($data as $col => $dir){
				if(!empty($this->orderData) && count($this->orderData)){
					array_push($this->orderData, $col, $dir);
				}
				else {
					$this->orderData = array($col, $dir);
				}
			}
		}
		return $this;
	}

	public function addLimitParams(int $data){
		array_push($this->limitData, $data);
		return $this;
	}

	public function addOffsetParams(int $data){
		array_push($this->offsetData, $data);
		return $this;
	}

	public function logicParamsProcess(){
		$data = func_get_args();
		if(count($data) == 3){
			$bindData = [$data[2]];
		}
		elseif(strpos($data[2], 'BETWEEN') !== false){
			$bindData = array($data[3], $data[4]);
		}elseif(strpos(strtoupper($data[2]), 'IN') !== false){
			$bindData = $data[3];
		}else{
			$bindData = [$data[3]];
		}
		if($data[0] == 'where'){
			$this->addWhereParams($bindData);
		}elseif($data[0] == 'on'){
			$this->addOnParams($bindData);
		}elseif($data[0] == 'having'){
			$this->addHavingParams($bindData);
		}
		return $this;
	}
}


 ?>
