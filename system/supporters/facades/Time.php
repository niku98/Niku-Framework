<?php
namespace system\supporters\facades;
use system\supporters\Time as RealTime;
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
