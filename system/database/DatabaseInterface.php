<?php

namespace System\database;

/**
 * Interface Class for all Database
 */
interface DatabaseInterface
{
	public function table($name);

	public function insert(array $data);
	public function update(array $data);
	public function delete();
	public function updateOrInsert(array $data);
	public function distinct();
	public function select($data = ['*']);

	public function join($tables, $column1, $operator = NULL, $column2 = NULL);
	public function leftJoin($tables, $column1, $operator = NULL, $column2 = NULL);
	public function rightJoin($tables, $column1, $operator = NULL, $column2 = NULL);

	public function where();
	public function orWhere();
	public function having();
	public function orHaving();

	public function orderBy($data);
	public function limit(int $data);
	public function offset(int $data);
}


 ?>
