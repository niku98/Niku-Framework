<?php
namespace System\Supporters\Facades;
use System\Requests\Session as RealSession;
/**
 * Session
 */
class Session extends Facade
{
	protected static function realClassName(){
		return RealSession::class;
	}

	protected static function isSingleton()
	{
		return true;
	}
}


 ?>
