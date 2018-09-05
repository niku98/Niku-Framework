<?php

namespace system\supporters\facades;
/**
 * Request class
 * Do: Get params from user request
 */
class Redirect extends Facade
{
	protected static function realClassName(){
		return 'system\\responses\\Redirect';
	}

	protected static function isSingleton()
	{
		return true;
	}
}


 ?>
