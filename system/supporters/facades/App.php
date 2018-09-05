<?php
namespace system\supporters\facades;
use system\app\App as Application;
/**
 *
 */
class App extends Facade
{
	protected static function realClassName(){
		return Application::class;
	}

	protected static function isSingleton()
	{
		return true;
	}
}



 ?>
