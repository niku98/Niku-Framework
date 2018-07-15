<?php
use system\supporters\Session;

/**
 * Class to process route
 */
class RouteProcess
{
	private static $routes = [];

	public function addRoute($route)
	{
		self::$routes[] = $route;
	}

	public static function getRequestUrl(){
		$dirName = dirname($_SERVER['PHP_SELF']);
		$requestUrl = urldecode($_SERVER['REQUEST_URI']);
		$lastUrl = ltrim(trim($requestUrl, '/'), trim($dirName, '/'));
		$lastUrl = explode('?', $lastUrl)[0];
		return trim($lastUrl, '/');
	}

	public function map(){
		$requestUrl = self::getRequestUrl();
		foreach (self::$routes as $route) {
			if($route->match($requestUrl)){
				$route->doAction();
				self::deleteAllRoutes();
				return true;
			}
		}
		self::deleteAllRoutes();
		return false;
	}

	public static function deleteAllRoutes(){
		self::$routes = NULL;
	}

	public function find($name)
	{
		foreach (self::$routes as $route) {
			if($route->name == $name){
				return $route;
			}
		}
	}
}


 ?>
