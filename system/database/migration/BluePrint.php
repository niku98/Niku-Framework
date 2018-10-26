<?php
namespace System\database\Migration;
use System\database\Database;

/**
 *
 */
class BluePrint
{
	private $name;
	private $changedName = '';
	private $properties = array();
	private $uniques = array();
	private $primaries = array();
	private $checks = array();
	private $foreigns = array();
	private $alter = false;

	public function __construct(string $name)
	{
		$this->name = $name;
		return $this;
	}

	public function __call($method, $params)
	{
		return $this->addProperty($method, ...$params);
	}

	public function addProperty($type, $name, $length = -1, $default = null)
	{
		if(strpos($type, 'increment') !== false || strpos($type, 'Increment') !== false){
			return $this->addIncrement($type, $name, $length);
		}

		$property = new BluePrintProperty($name, strtoupper($type), $length);

		if($default !== null){
			$property->default($default);
		}

		$this->properties[] = $property;
		return $property;
	}

	public function name($name)
	{
		$this->changedName = $name;
		return $this;
	}

	public function foreignKey($col_name)
	{
		return $this->addProperty('', $col_name)->foreignKey();
	}

	public function design()
	{
		$sql = 'CREATE TABLE '.$this->name.' ('.$this->getColumnsStatement().', '.$this->getConstraintsSatement().')';
		// die($sql);
		// die();
		Database::query($sql);
	}

	public function alter()
	{
		if($this->changedName !== ''){
			$this->changeName();
		}

		$this->alter = true;

		$sql = 'ALTER TABLE '.$this->name.' ( '.$this->getColumnsStatement().', '.$this->getConstraintsSatement().' )';
		Database::query($sql);
	}

	private function changeName()
	{
		try {
			$sql = "ALTER TABLE $this->name TO $this->changedName";
			Database::query($sql);
			$this->name = $this->changedName;
			$this->changedName = '';

			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	private function getColumnsStatement()
	{
		$sqls = array();
		foreach ($this->properties as $property ) {
			$sql = $this->processColumnSatement($property);
			if($sql !== null){
				$sqls[] = $sql;
			}
		}

		return implode($sqls, ', ');
	}

	private function processColumnSatement($property)
	{
		if($property->isUnique()){
			$this->uniques[] = $property;
		}elseif($property->isPrimary()){
			$this->primaries[] = $property;
		}elseif($property->isForeignKey()){
			$this->foreigns[] =$property;
		}elseif($property->hasCheck()){
			$this->checks[] = $property;
		}

		if($this->alter === false){
			return $this->processAddColumnStatement($property);
		}

		return $this->processAlterColumnSatement($property);
	}

	private function processAddColumnStatement($property)
	{
		if($property->getType() == ''){
			return null;
		}

		$sql = $property->getName().' '.$property->getType();
		if($property->getLength() !== -1){
			$sql .= '('.$property->getLength().')';
		}

		if($property->hasDefault()){
			$sql .= ' DEFAULT '.(strpos($property->getDefault(), '()') === strlen($property->getDefault()) - 3 ? $property->getDefault() : "'{$property->getDefault()}'");
		}elseif(!$property->isNullable()){
			$sql .= ' NOT NULL';
		}

		return $sql;
	}

	private function getConstraintsSatement()
	{
		$sqls = array();
		$unique_sql = $this->getUniqueConstraintSatement();
		$primary_sql = $this->getPrimaryKeyConstraintSatement();
		$foreign_sql = $this->getForeignConstraintSatement();
		$check_sql = $this->getCheckConstraintSatement();

		if($unique_sql !== null){
			$sqls[] = $unique_sql;
		}

		if($primary_sql !== null){
			$sqls[] = $primary_sql;
		}

		if($foreign_sql !== null){
			$sqls[] = $foreign_sql;
		}

		if($check_sql !== null){
			$sqls[] = $check_sql;
		}

		return implode($sqls, ', ');
	}

	private function getUniqueConstraintSatement(){
		if(count($this->uniques) === 0){
			return null;
		}

		$sql = $this->alter ? 'ADD' : '';
		$sql .= ' CONSTRAINT UNI_'.$this->name.' UNIQUE ( ';
		foreach ($this->uniques as $property) {
			$sql .= $property->getName().', ';
		}

		$sql = rtrim($sql, ', ');
		$sql .= ' )';
		return $sql;
	}

	private function getPrimaryKeyConstraintSatement()
	{
		if(count($this->primaries) === 0){
			return null;
		}

		$sql = $this->alter ? 'ADD' : '';
		$sql .= ' CONSTRAINT PK_'.$this->name.' PRIMARY KEY ( ';
		foreach ($this->primaries as $property) {
			$sql .= $property->getName().', ';
		}

		$sql = rtrim($sql, ', ');
		$sql .= ' )';
		return $sql;
	}

	private function getForeignConstraintSatement()
	{
		if(count($this->foreigns) === 0){
			return null;
		}

		$sqls = array();

		foreach ($this->foreigns as $property) {
			$sql = $this->alter ? 'ADD' : '';
			$sql .= ' CONSTRAINT FK_'.$this->name.'_'.$property->getName().' FOREIGN KEY ( ';
			$sql .= $property->getName().' ) REFERENCES '.$property->getOnTable().'( '.$property->getReferences().' )';

			$sqls[] = $sql;
		}

		return implode($sqls, ', ');
	}

	private function getCheckConstraintSatement()
	{
		if(count($this->checks) === 0){
			return null;
		}

		$sql = $this->alter ? 'ADD' : '';
		$sql .= ' CONSTRAINT CHK_'.$this->name.' CHECK ( ';
		foreach ($this->checks as $property) {
			$checks = array();
			foreach ($property->getCheck() as $check) {
				$checks[] = $property->getName().' '.$check;
			}

			$sql .= implode($checks, ' AND ');
		}
		$sql .= ' )';
		return $sql;
	}
}


 ?>
