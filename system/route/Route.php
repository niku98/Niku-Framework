<?php
use system\app\AppException;


/**
* Router for dectect uri
*/
class Route
{
	private static $prefix;
	private $url_show;
	private $data = [];

	public function __construct($base_url, $method, $action){
		$params = array();
		self::$prefix = RoutePrefix::getInstance();

		if(is_string($base_url))
			$base_url = trim(self::$prefix->url, '/').'/'.trim($base_url, '/');

		if(is_array($base_url)){
			$params = $base_url;
			unset($params[0]);

			$base_url = trim(self::$prefix->url, '/').'/'.trim($base_url[0], '/');
		}

		foreach (self::$prefix->middlewares as $middleware) {
			if(!is_array($middleware)){
				$routeMiddlewares[] = $middleware;
			}else{
				foreach ($middleware as $midware) {
					$routeMiddlewares[] = $midware;
				}
			}
		}

		$this->data = [
			'base_url' => $base_url,
			'url' => $base_url,
			'method' => $method,
			'action' => new RouteAction($action),
			'parameters' => $params
		];

		$this->url_show = $base_url;

		if(!empty($routeMiddlewares)){
			$this->data['middlewares'] = $routeMiddlewares;
		}

		RouteProcess::addRoute($this);
		return $this;
	}

	public function __set($key, $value){
		$this->data[$key] = $value;
	}

	public function __get($key)
	{
		return !empty($this->data[$key]) ? $this->data[$key] : null;
	}

	public function __debugInfo(){
		return $this->data;
	}

	public function __toString(){
		return url($this->url_show);
	}

	public function get($uri, $action){
		return Route::add($uri, 'GET', $action);
	}

	public function post($uri, $action){
		return Route::add($uri, 'POST', $action);
	}

	public function put($uri, $action){
		return Route::add($uri, 'PUT', $action);
	}

	public function delete($uri, $action){
		return Route::add($uri, 'DELETE', $action);
	}

	public function any($uri, $action){
		return Route::add($uri, 'any', $action);
	}

	public function add($base_url, $method, $action){
		return new Route($base_url, $method, $action);
	}

	public function name($name){
		$this->data['name'] = $name;
		return $this;
	}

	public function middleware($name){
		$this->data['middlewares'][] = $name;
		return $this;
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
			self::add('', 'any', $defaultAction);
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

	public function makeUrl(){

		if(count($this->data['parameters'])){
			$params = func_get_args();
			$partUrls = explode('/', $this->url);
			$countParam = 0;
			$this->url_show = '';
			foreach ($partUrls as $part) {
				if(isRegularExpression($part)){
					if(!empty($params[$countParam])){
						if(preg_match('/'.$part.'/', $params[$countParam])){
							$this->url_show .= $params[$countParam++].'/';
						}else{
							throw new AppException("Regular Expression error $params[$countParam] not match $part");
							die();
						}
					}else{
						throw new AppException("Parameters for Route('$this->name') is not enough!");
						die();
					}
				}else{
					$this->url_show .= $part.'/';
				}
			}

			$this->url_show = rtrim($this->url_show, '/');
		}
		return $this;
	}

	//Check part in user url with part url in route, include method check
	public function match($uri){
		$methodCheck = false;
		if(!empty($this->data['method']) && $this->method != 'any'){
			$listMed = explode('|', $this->data['method']);
			foreach ($listMed as $value) {
				if(strtoupper($value) == $_SERVER['REQUEST_METHOD']){
					$methodCheck = true;
					break;
				}
			}
		}else{
			$methodCheck = true;
		}

		$base = trim($this->base_url, '/');

		if($base == $uri && $methodCheck){
			return $this->checkMiddleware();
		}

		$listBase = explode('/', $base);
		$listUri = explode('/', $uri);
		if(count($listBase) != count($listUri))
			return false;


		$matchCount = 0;
		for ($i=0, $j = 1; $i < count($listBase); $i++) {
			$base = $listBase[$i];
			$uri = $listUri[$i];
			if($base == $uri)
			$matchCount++;
			else{
				$base = '/'.$base.'/';
				preg_match_all($base,$uri, $matched, PREG_PATTERN_ORDER);
				if(!empty($matched[0][0]) && $matched[0][0] == $uri){
					$matchCount++;
					if(count($this->data['parameters'])){
						for ($jum=1; $jum < count($matched); $jum++) {
							$_GET[$this->data['parameters'][$j++]] = $matched[$jum][0]; // Add to GET variable if route url has params
						}
					}
				}
			}
		}


		$result = ($matchCount === count($listBase)) && $methodCheck && $this->checkMiddleware();
		return $result;
	}

	protected function checkMiddleware(){
		if(!empty($this->data['middlewares'])){
			$middlewareCheck = false;

			foreach ($this->data['middlewares'] as $middleware) {
				if(is_callable($middleware)){
					$middlewareCheck = $middleware();
					if(is_object($middlewareCheck)){
						if(strpos(get_class($middlewareCheck), 'Redirect') !== false){
							$middlewareCheck->go();
							die();
						}
					}
				}else{
					$midClass = 'middlewares\\'.$middleware;
					$midClass = new $midClass();
					$middlewareCheck = $midClass->handle();
					if(is_object($middlewareCheck)){
						if(strpos(get_class($middlewareCheck), 'Redirect') !== false){
							$middlewareCheck->go();
							die();
						}
					}
					unset($midClass);
				}
			}
			return $middlewareCheck;
		}
		return true;
	}

	public function doAction(){
		$this->action->do($this->data['parameters']);
	}
}


?>
