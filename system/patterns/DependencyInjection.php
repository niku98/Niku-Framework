<?php
namespace system\parterns;

/**
 * Dependency Injection for app
 */
class DependencyInjection extends Singletion
{
	/**
	 * Content class need inject
	 */
	protected $class;

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
