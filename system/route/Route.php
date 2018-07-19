<?php
use system\app\AppException;


/**
* Router for dectect uri
*/
class Route
{
	private static $prefix;
	private static $routers = array();

	public function get($uri, $action){
		return Route::addRoute($uri, ['GET'], $action);
	}

	public function post($uri, $action){
		return Route::addRoute($uri, ['POST'], $action);
	}

	public function put($uri, $action){
		return Route::addRoute($uri, ['PUT'], $action);
	}

	public function delete($uri, $action){
		return Route::addRoute($uri, ['DELETE'], $action);
	}

	public function options($uri, $action){
		return Route::addRoute($uri, ['OPTIONS'], $action);
	}

	public function any($uri, $action){
		return Route::addRoute($uri, [], $action);
	}

	public function match(array $methods, string $uri, $action){
		return Route::addRoute($uri, $methods, $action);
	}

	public function addRoute($base_url, $method, $action){
		self::$prefix = RoutePrefix::getInstance();
		if(self::$prefix->url !== ''){
			$base_url = trim(self::$prefix->url, '/').'/'.trim($base_url, '/');
		}
		$router = new Router($base_url, $method, $action);
		self::$routers[] = $router;
		return $router;
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
		foreach (self::$routers as $router) {
			if($router->match($requestUrl)){
				$router->doAction();
				self::deleteAllRoutes();
				return true;
			}
		}
		self::deleteAllRoutes();
		return false;
	}

	public static function deleteAllRoutes(){
		self::$routers = NULL;
	}

	public function find($name)
	{
		foreach (self::$routers as $router) {
			if($router->name == $name){
				return $router;
			}
		}
	}

	public function prefix($prefix, $defaultAction, $group = null){
		self::$prefix = RoutePrefix::getInstance();
		$prefix_url = '';

		if(is_array($prefix)){
			if(!empty($prefix['url'])){
				$prefix_url = $prefix['url'];
			}

			if(!empty($prefix['middlewares'])){
				$mids = self::$prefix->middlewares;

				if(!is_array($prefix['middlewares']))
					$prefix['middlewares'] = array($prefix['middlewares']);

				array_push( $mids, $prefix['middlewares']);
				self::$prefix->middlewares = $mids;
			}
		}else{
			$prefix_url = $prefix;
		}

		self::$prefix->url = trim(self::$prefix->url, '/').'/'.trim($prefix_url,'/');

		$paramCount = count(func_get_args());

		if($paramCount > 2){
			self::addRoute('', 'GET', $defaultAction);
			if(is_callable($group)){
				$group();
			}
		}else{
			$defaultAction();
		}

		$partPrefix = explode('/', self::$prefix->url);
		array_pop($partPrefix);
		self::$prefix->url = trim(implode($partPrefix, '/'), '/');

		$mids = self::$prefix->middlewares;
		array_pop($mids);
		self::$prefix->middlewares = $mids;

		unset($mids);
	}
}


?>
