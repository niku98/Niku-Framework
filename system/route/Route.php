<?php
namespace System\Route;
use \AppException;
use System\Route\Exception\NotFoundException;

/**
* Router for dectect uri
*/
class Route
{
	private static $prefix;
	private static $routers = array();
	private static $current;

	public static function get($uri, $action){
		return Route::addRoute($uri, ['GET'], $action);
	}

	public static function post($uri, $action){
		return Route::addRoute($uri, ['POST'], $action);
	}

	public static function put($uri, $action){
		return Route::addRoute($uri, ['PUT'], $action);
	}

	public static function patch($uri, $action){
		return Route::addRoute($uri, ['PUT'], $action);
	}

	public static function delete($uri, $action){
		return Route::addRoute($uri, ['DELETE'], $action);
	}

	public static function options($uri, $action){
		return Route::addRoute($uri, ['OPTIONS'], $action);
	}

	public static function any($uri, $action){
		return Route::addRoute($uri, [], $action);
	}

	public static function match(array $methods, string $uri, $action){
		return Route::addRoute($uri, $methods, $action);
	}

	public static function addRoute($base_url, $method, $action){
		self::$prefix = RoutePrefix::getInstance();
		if(self::$prefix->url !== ''){
			$base_url = trim(self::$prefix->url, '/').'/'.trim($base_url, '/');
		}
		$router = new Router($base_url, $method, $action);

		if(self::$prefix->middlewares != []){
			$router->middleware(self::$prefix->middlewares);
		}

		self::$routers[] = $router;
		return $router;
	}

	public static function getRequestUrl(){
		$dirName = dirname($_SERVER['PHP_SELF']);
		$requestUrl = urldecode($_SERVER['REQUEST_URI']);
		$lastUrl = str_replace(trim($dirName, '/'), '', trim($requestUrl, '/'));
		$lastUrl = explode('?', $lastUrl)[0];
		return trim($lastUrl, '/');
	}

	public static function map(){
		$requestUrl = self::getRequestUrl();

		if(strpos($requestUrl, 'api') === 0){
			Route::prefix('api', function(){
				loadFile('routes/api.php');
			});
		}else{
			loadFile('routes/web.php');
		}

		foreach (self::$routers as $router) {
			if($router->match($requestUrl)){
				$router->matchTokenIfNeeded();
				Route::$current = $router;
				$router->doAction();
				return;
			}
		}
		throw new NotFoundException("404, Page Not Found!");

	}

	public static function find($name)
	{
		foreach (self::$routers as $router) {
			if($router->name == $name){
				return $router;
			}
		}
	}

	public static function current(){
		return static::$current;
	}

	public static function prefix($prefix, $defaultAction, $group = null){
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

				array_push( $mids, ...$prefix['middlewares']);
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

		self::$prefix->url = str_replace(trim($prefix_url, '/'), '', self::$prefix->url);

		if(!empty($prefix['middlewares'])){
			$mids = self::$prefix->middlewares;

			foreach ($prefix['middlewares'] as $k => $mid) {
				$key = array_search($mid, $mids);
				if($key){
					unset($mids[$key]);
				}
			}

			self::$prefix->middlewares = $mids;

			unset($mids);
		}
	}
}


?>
