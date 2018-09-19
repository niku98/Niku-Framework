<?php
namespace system\supporters\facades;
use system\requests\Session as RealSession;
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
