<?php
namespace system\model;
use system\database\Database;
use system\model\Collection;
use \AppException;
use Request;

class Builder
{
	/**
	 * Current Model
	 *
	 * @param
	 * @return    void
	 * @author
	 * @copyright
	 */
	private $model;

	/**
	 * Database Connection
	 *
	 * @param
	 * @return    void
	 * @author
	 * @copyright
	 */
	private $db;


	public function __construct(Model $model)
	{
		$this->model = $model;

		$this->db = Database::table($model->getTable());
	}

	public function __call($method, $params)
	{
		$this->db->$method(...$params);
		return $this;
	}

	/*----------------------------------------
	BUILDER METHODS
	----------------------------------------*/

	public function pagination(int $per_page)
	{
		$page = Request::has('page') ? Request::get('page') : 1;
		$offset = ($page - 1) * $per_page;

		$db = clone $this->db;

		$list_array = $db->limit($per_page)->offset($offset)->get();
		$items = array();

		foreach ($list_array as $array) {
			$items[] = $this->model->newInstance($array);
		}

		return $this->db->pagination($per_page, $items);
	}

	public function insert(){
		return $this->db->insert(...func_get_args());
	}

	public function update(){
		return $this->db->update(...func_get_args());
	}

	public function delete(){
		return $this->db->delete(...func_get_args());
	}

	public function count(){
		return $this->db->count();
	}

	public function first(){
		$result = $this->db->first();
		if(!$result)
			return null;

		return $this->model->newInstance($result);
	}

	public function get(){
		$list_array = $this->db->get();

		$list_object = [];
		foreach ($list_array as $array) {
			$list_object[] = $this->model->newInstance($array);
		}
		return new Collection($list_object);
	}
}

 ?>
