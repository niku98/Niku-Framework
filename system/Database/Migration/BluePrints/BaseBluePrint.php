<?php
namespace System\Database\Migration\BluePrints;
use System\Database\Database;
use System\Database\Migration\BluePrintProperty;

/**
 *
 */
abstract class BaseBluePrint
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

	public function rename($name)
	{
		$this->name($name);
		$this->changeName();
		return $this;
	}

	public function foreignKey($col_name)
	{
		return $this->addProperty('', $col_name)->foreignKey();
	}

	public function design()
	{
		$sql = 'CREATE TABLE '.$this->name.' ('.$this->getColumnsStatement().($this->getConstraintsSatement() != '' ? ', '.$this->getConstraintsSatement() : '').')';
		Database::query($sql);
	}

	public function alter()
	{
		if($this->changedName !== ''){
			$this->changeName();
		}

		$this->alter = true;

		$sql = 'ALTER TABLE '.$this->name.' ( '.$this->getColumnsStatement().($this->getConstraintsSatement() != '' ? ', '.$this->getConstraintsSatement() : '').' )';
		die($sql);
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
}


 ?>
