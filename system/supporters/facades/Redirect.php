<?php

namespace system\supporters\facades;
use system\responses\Redirect as RealRedirect;
/**
 * Request class
 * Do: Get params from user request
 */
class Redirect extends Facade
{
	protected static function realClassName(){
		return RealRedirect::class;
	}

	protected static function isSingleton()
	{
		return true;
	}
}


 ?>
