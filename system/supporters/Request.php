<?php

namespace system\supporters;
use system\supporters\File;
/**
 * Request class
 * Do: Get params from user request
 */
class Request
{
	private static $data = array();

	public function __construct(){
		self::$data = $_GET;
		self::$data = array_merge(self::$data, $_POST);

		$post_data = [];
		parse_str(file_get_contents("php://input"), $post_data);

		self::$data = array_merge(self::$data, $post_data);
		self::$data = array_merge(self::$data, $_FILES);
		self::$data = array_merge(self::$data, $_COOKIE);
		self::$data['url'] = current_url();
		self::$data['previous_url'] = $_SERVER['HTTP_REFERER'] ?? '';
	}

	public function __debugInfo(){
		return self::$data ?? NULL;
	}

	public function __get($key){
		return self::$data[$key] ?? NULL;
	}

	public function get($param){
		return $_GET[$param] ?? NULL;
	}

	public function post($param)
	{
		return $_POST[$param] ?? NULL;
	}

	public function has($value)
	{
		return isset(self::$data[$value]);
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

		if(empty($_FILES[$name]['name'])){
			if(!empty($_FILES[$name][0]['name'])){
				$list = array();
				foreach ($$_FILES[$name] as $file) {
					$list[] = new File($_FILES[$name], 'uploaded');
				}

				return $list;
			}

			return NULL;
		}

		return new File($_FILES[$name], 'uploaded');
	}

	public function all(){
		return self::$data;
	}

	public function cookie($param){
		return $_COOKIE[$param];
	}

	public function validate(array $rules, array $message = array()){
		validate(self::$data, $rules, $message);
	}
}


 ?>
