<?php
namespace system\requests;
use system\supporters\File;
use system\patterns\abstracts\HasDataProperty;

/**
 * Request class
 * Do: Get params from user request
 */
class Request
{
	use HasDataProperty;
	private $headers = array();
	public $ok = '';

	public function __construct(){
		parse_str(file_get_contents("php://input"),$post_vars);

		$this->data = $post_vars;

		$this->data = array_merge($this->data, $_REQUEST);

		//Get Header Information
		$this->headers = getallheaders();
	}

	public function url()
	{
		return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}

	public function previous_url()
	{
		return $this->server('HTTP_REFERER');
	}

	public function get($param = NULL){
		if(is_null($param)){
			return $_GET;
		}
		return $_GET[$param] ?? NULL;
	}

	public function param($key)
	{
		return $this->data[$key] ?? NULL;
	}

	public function post($param = NULL)
	{
		if(is_null($param)){
			return $_POST;
		}
		return $_POST[$param] ?? NULL;
	}

	public function has($value)
	{
		return isset($this->data[$value]);
	}

	public function hasFile($key){
		if(empty($_FILES[$key]['name'])){
			if(!empty($_FILES[$key][0]['name'])){
				return true;
			}

			return false;
		}

		return true;
	}

	public function file($name){
		if(!self::hasFile($name))
			return NULL;

		if(is_array($_FILES[$name]['name'])){

			$_FILES = reArrayFiles($_FILES);

			$list = array();
			foreach ($_FILES[$name] as $file) {
				$list[] = new File($file, 'uploaded');
			}

			return $list;
		}

		return new File($_FILES[$name], 'uploaded');
	}

	public function all(){
		return array_merge($this->data, $_FILES);
	}

	public function cookie($param = NULL){
		if(is_null($param)){
			return $_COOKIE;
		}
		return $_COOKIE[$param];
	}

	public function header($key = NULL)
	{
		if(is_null($key)){
			return $this->headers;
		}

		return $this->headers[$key] ?? NULL;
	}

	public function server($key = NULL)
	{
		if(is_null($key)){
			return $_SERVER;
		}
		return $_SERVER[$key] ?? NULL;
	}

	public function method()
	{
		return $this->server('REQUEST_METHOD');
	}

	public function isMethod(string $method)
	{
		return strtoupper($method) === strtoupper($this->method());
	}

	public function isAjax()
	{
		return strtolower($this->server('HTTP_X_REQUESTED_WITH')) === strtolower('xmlhttprequest');
	}

	public function validate(array $rules, array $message = array()){
		validate(array_merge($this->data, $_FILES), $rules, $message);
	}
}


 ?>
