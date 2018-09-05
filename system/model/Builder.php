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

	/*----------------------------------------
	BUILDER METHODS
	----------------------------------------*/

	public function select($data = ['*'])
	{
		$data = is_array($data) ? $data : func_get_args();
		$this->db->select($data);
		return $this;
	}

	public function join($tables){
		$this->db->join($tables);
		return $this;
	}

	public function leftJoin($tables){
		$this->db->leftJoin($tables);
		return $this;
	}

	public function rightJoin($tables){
		$this->db->rightJoin($tables);

		return $this;
	}

	public function where(){
		$this->db->where(...func_get_args());
		return $this;
	}

	public function andWhere(){
		$this->db->andWhere(...func_get_args());

		return $this;
	}

	public function orWhere(){
		$this->db->orWhere(...func_get_args());

		return $this;
	}

	public function on(){
		$this->db->on(...func_get_args());
		return $this;
	}

	public function andOn(){
		$this->db->andOn(...func_get_args());
		return $this;
	}

	public function orOn(){
		$this->db->orOn(...func_get_args());
		return $this;
	}

	public function having(){
		$this->db->having(...func_get_args());
		return $this;
	}

	public function andHaving(){
		$this->andHaving(...func_get_args());
		return $this;
	}

	public function orHaving(){
		$this->db->orHaving(...func_get_args());
		return $this;
	}

	public function groupBy($data){
		$this->db->groupBy($data);
		return $this;
	}

	public function orderBy($data){
		$this->db->orderBy($data);

		return $this;
	}

	public function limit(int $data){
		$this->db->limit($data);
		return $this;
	}

	public function offset(int $data){
		$this->db->offset($data);
		return $this;
	}

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
