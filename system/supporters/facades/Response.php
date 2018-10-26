<?php

namespace System\Supporters\Facades;
use System\Responses\Response as RealResponse;
/**
 * Response class
 */
class Response extends Facade
{
	protected static function realClassName(){
		return RealResponse::class;
	}

	protected static function isSingleton()
	{
		return false;
	}
}


 ?>
