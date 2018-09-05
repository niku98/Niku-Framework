<?php

namespace system\supporters\facades;
/**
 * Request class
 * Do: Get params from user request
 */
class Request extends Facade
{
	protected static function realClassName(){
		return 'system\\requests\\Request';
	}

	protected static function isSingleton()
	{
		return true;
	}
}


 ?>
