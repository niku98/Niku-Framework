<?php
namespace System\Supporters\Facades;
use System\App\App as Application;
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
