<?php
namespace System\Database\Migration\BluePrints;
/**
 *
 */
class MysqlBluePrint extends BaseBluePrint
{
	protected function processAddColumnStatement($property)
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

	protected function processAlterColumnSatement($property)
	{
		return 'asdf';
	}

	protected function getUniqueConstraintSatement(){
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

	protected function getPrimaryKeyConstraintSatement()
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

	protected function getForeignConstraintSatement()
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

	protected function getCheckConstraintSatement()
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
