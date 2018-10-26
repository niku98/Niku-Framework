<?php
namespace System\middlewares;

/**
 * Base Middleware
 * Use to check conditions
 */
abstract class BaseMiddleware
{
	abstract public static function handle();

	public function getResult(){
		return self::handle();
	}
}


 ?>
