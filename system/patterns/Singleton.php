<?php
namespace system\patterns;
/**
 * Singleton Abstract Class
 */
abstract class Singleton
{

	private static $instance = [];
	protected function __construct()
	{
	}

	public function getInstance(){
		$class = get_called_class();
		if(empty(self::$instance[$class])){
			self::$instance[$class] = new $class(...func_get_args());
		}
		return self::$instance[$class];
	}
}


 ?>
