<?php
namespace system\supporters\facades;

/**
 * Session
 */
class Session extends Facade
{
	protected static function realClassName(){
		return 'system\\requests\\Session';
	}

	protected static function isSingleton()
	{
		return true;
	}
}


 ?>
