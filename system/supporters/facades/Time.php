<?php
namespace System\Supporters\Facades;
use System\Supporters\Time as RealTime;
/**
 * Session
 */
class Time extends Facade
{
	protected static function realClassName(){
		return RealTime::class;
	}

	protected static function isSingleton()
	{
		return true;
	}
}


 ?>
