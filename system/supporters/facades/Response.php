<?php

namespace system\supporters\facades;
use system\responses\Response as RealResponse;
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
