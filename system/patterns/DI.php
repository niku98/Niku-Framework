<?php
namespace System\parterns;

/**
 * Dependency Injection for app
 */
class DI
{
	public static function create($class)
	{
		$this->addClass($class);
		return $this->createObject();
	}

	public function createObject()
	{

	}
}


 ?>
