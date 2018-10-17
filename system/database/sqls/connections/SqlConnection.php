<?php
namespace system\database\sqls\connections;

/**
 * Sql Connection
 */
abstract class SqlConnection
{
	protected $conn;
	protected $query = false;
	protected $result = false;
	protected $bindParams = [];

	public $affected_rows = 0;
	public $insert_id = 0;
	public $num_rows = -1;

	/*----------------------------------------
	DATA PROPERTIES
	----------------------------------------*/
	protected $whereData = [];
	protected $insertData = [];
	protected $insertManyData = [];
	protected $updateData = [];
	protected $onData = [];
	protected $havingData = [];
	protected $orderData = [];
	protected $limitData = [];
	protected $offsetData = [];

	public function resetData(){
		$this->whereData = array();
		$this->insertData = array();
		$this->insertManyData = array();
		$this->updateData = array();
		$this->onData = array();
		$this->havingData = array();
		$this->orderData = array();
		$this->limitData = array();
		$this->offsetData = array();
		$this->bindParams = array();
		$this->result = false;
	}

	/*----------------------------------------
	ABSTRACT METHODS
	----------------------------------------*/

	abstract public function exec(string $sql);
	abstract public function close();
	abstract protected function addWhereParams(array $data);
	abstract public function addInsertParams(array $data);
	abstract public function addInsertManyParams(array $data);
	abstract public function addUpdateParams(array $data);
	abstract public function addOnParams(array $data);
	abstract public function addHavingParams(array $data);
	abstract public function addOrderParams(array $data);
	abstract public function addLimitParams(int $data);
	abstract public function addOffsetParams(int $data);
}


 ?>
