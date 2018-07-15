<?php

namespace system\database;

/**
 * Interface Class for all Database
 */
interface DatabaseInterface
{
	public static function table($name);

	public function insert(array $data);
	public function update(array $data);
	public function delete();
	public function updateOrInsert(array $data);
	public function distinct();
	public function select($data = ['*']);

	public function join($tables);
	public function leftJoin($tables);
	public function rightJoin($tables);

	public function where();
	public function andWhere();
	public function orWhere();
	public function on();
	public function andOn();
	public function orOn();
	public function having();
	public function andHaving();
	public function orHaving();

	public function orderBy($data);
	public function limit(int $data);
	public function offset(int $data);
}


 ?>
