<?php

namespace System\Supporters\Facades;
use System\Requests\Request as RealRequest;

/**
 * Request class
 * Do: Get params from user request
 */
class Request extends Facade
{
	protected static function realClassName(){
		return RealRequest::class;
	}

	protected static function isSingleton()
	{
		return true;
	}
}


 ?>
