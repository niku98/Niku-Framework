<?php
namespace system\route;

use system\patterns\Singleton;
/**
 * Prefix Class | Support for Route
 */
class RoutePrefix extends Singleton
{
	private static $instance;
	private $data;

	protected function __construct()
	{
		$this->data['middlewares'] = array();
	}

	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function __get($key)
	{
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	public function middleware($name){
		$this->data['middlewares'][] = $name;
		return $this;
	}
}

 ?>
