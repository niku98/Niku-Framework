<?php
namespace System\Route;

use System\Supporters\Facades\Request;
use \AppException;
use System\Supporters\CsrfToken;
use Session;
use System\Route\Exception\TokenException;

/**
 * Router
 */
class Router
{
	/**
	 * Base Router uri
	 *
	 * @var string
	 */
	private $uri;

	/**
	 * Router uri to show
	 *
	 * @var string
	 */
	private $uri_show;

	/**
	 * Router name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Content Router methods
	 *
	 * @var array
	 */
	private $methods = array();

	/**
	 * Content Router middlewares
	 *
	 * @var array
	 */
	private $middlewares = array();

	/**
	 * Content Router action
	 *
	 * @var array
	 */
	private $action;

	/**
	 * Content Router parameters
	 *
	 * @var array
	 */
	private $parameters = array();

	public function __construct($uri, $methods, $action){
		$this->uri = trim($uri, '/');

		$this->parseParameters();

		$this->methods = is_string($methods) ? [$methods] : $methods;
		$this->action = new RouteAction($action);

		return $this;
	}

	/**
	 * __toString magic method, return url
	 *
	 * @param     void
	 * @return    string
	 */
	public function __toString(){
		return url($this->uri_show);
	}

	/**
	 * Name this router.
	 *
	 * @param	  string $name
	 * @return    System\Route\Router
	 */
	public function name($name){
		$this->name = $name;
		return $this;
	}

	/**
	 * Add Middleware to this router
	 *
	 * @param	  string
	 * @return    System\Route\Router
	 */
	public function middleware($name){
		$this->middlewares[] = $name;
		return $this;
	}


	/**
	 * Add rules for Router Parameters
	 *
	 * @param	  array $rules
	 * @return    Router
	 */
	public function where(array $rules)
	{
		foreach ($rules as $param => $rule) {
			$this->parameters[$param] = $rule;
		}

		return $this;
	}

	/**
	 * Parse parameters from uri
	 *
	 * @param	  void
	 * @return    void
	 */
	protected function parseParameters(){
		$uri_parts = explode('/', $this->uri);
		foreach ($uri_parts as $base_part) {
			$changed_part = trim($base_part, '}');
			$changed_part = trim($changed_part, '{');

			if($changed_part != $base_part){
				if(strpos($changed_part, '?') !== false){
					$changed_part = trim($changed_part, '?');
				}
				$this->parameters[$changed_part] = '';
			}
		}
	}

	/**
	 * Make Uri before convert to string type
	 *
	 * @param	  array $data
	 * @return    Router
	 */
	public function makeUrl(array $data = array()){
		$base_parts = explode('/', $this->uri);
		$this->uri_show = '';

		foreach ($base_parts as $k => $base) {
			$regex = '';
			$last_regex = '';

			if(strpos($base, '{') !== false && strpos($base, '}') !== false){
				$base = trim(trim($base, '{'), '}');

				if(strpos($base, '?') !== false){
					$base = trim($base, '?');
					$last_regex = '?';
				}

				if(!empty($this->parameters[$base])){
					$base_regex = trim(trim($this->parameters[$base], '('), ')');
					$regex = '('.$base_regex.')'.$last_regex;
				}else{
					$regex = '([a-zA-Z\d-_]+)'.$last_regex;
				}
			}else{
				$this->uri_show .= $base.'/';
				continue;
			}

			if(strpos($regex, '?') !== false){
				if(empty($data[$base])){
					continue;
				}
			}else{
				if(empty($data[$base])){
					$routeName = $this->name;
					throw new AppException("Param [$base] is required for Route [$routeName]!");
				}
			}

			if(preg_match_all('/'.$regex.'/', $data[$base], $matches)){
				if(!empty($matches[0][0])){
					$this->uri_show .= $data[$base].'/';
				}
			}
		}

		$this->uri_show = rtrim($this->uri_show, '/');

		return $this;
	}

	/**
	 * Check part in user url with part url in route, include method check
	 *
	 * @param	  string $uri
	 * @return    bool
	 * @author
	 * @copyright
	 */
	public function match($uri){
		return $this->matchUri($uri) && $this->matchMethod() && $this->checkMiddleware();
	}


	/**
	 * Check if request uri is matched to Router Uri
	 *
	 * @param	  string $uri
	 * @return    bool
	 */
	protected function matchUri(string $uri)
	{
		$uri = trim($uri, '/');
		if(!count($this->parameters)){
			return trim($this->uri, '/') === $uri;
		}

		$base_parts = explode('/', $this->uri);
		$check_parts = explode('/', $uri);

		$full_matches = count($base_parts);
		$matched = 0;

		foreach ($base_parts as $k => $base) {
			$regex = '';
			$last_regex = '';

			if(strpos($base, '{') !== false && strpos($base, '}') !== false){
				$base = trim(trim($base, '{'), '}');

				if(strpos($base, '?') !== false){
					$base = trim($base, '?');
					$last_regex = '?';
				}

				if(!empty($this->parameters[$base])){
					$base_regex = trim(trim($this->parameters[$base], '('), ')');
					$regex = '('.$base_regex.')'.$last_regex;
				}else{
					$regex = '([a-zA-Z\d-_]+)'.$last_regex;
				}
			}else{
				$regex = $base;
			}

			if(strpos($regex, '?') !== false){
				if(empty($check_parts[$k])){
					$matched++;
					continue;
				}
			}else{
				if(empty($check_parts[$k])){
					return false;
				}
			}

			if(preg_match_all('/'.$regex.'/', $check_parts[$k], $matches)){
				if(!empty($matches[0][0]) && $matches[0][0] === $check_parts[$k]){
					$_GET[$base] = $check_parts[$k];
					Request::getInstance()->$base = $check_parts[$k];
					$matched++;
				}
			}
		}

		return $matched === $full_matches;
	}


	/**
	 * Check if Current Request Method is in Router methods
	 * If not return false
	 *
	 * @param	  void
	 * @return    bool
	 */
	protected function matchMethod(){
		if(!empty($this->methods)){
			foreach ($this->methods as $method) {
				if(strtoupper($method) == $_SERVER['REQUEST_METHOD']){
					return true;
				}
			}

			return false;
		}

		return true;
	}


	/**
	 * Check if Current Request can pass to Route Middlewares
	 * If not return false
	 *
	 * @param	  void
	 * @return    bool
	 */
	protected function checkMiddleware(){
		if(!empty($this->middlewares)){
			$middlewareCheck = false;

			foreach ($this->middlewares as $middleware) {
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
			return $middlewareCheck;
		}
		return true;
	}

	public function matchTokenIfNeeded()
	{
		$request = new Request;
		if(!Request::isMethod('get') && !Request::isMethod('options')){
			if(!Request::has('csrf_token') || CsrfToken::checkToken($request->csrf_token) === false || $request->csrf_token !== Session::get('csrf_token')){
				throw new TokenException();
			}
		}
	}

	public function doAction(){
		$this->action->do();
	}
}


 ?>
