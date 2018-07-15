<?php

namespace system\supporters;
use system\supporters\File;
/**
 * Request class
 * Do: Get params from user request
 */
class Request
{
	private $data = array();

	public function __construct(){
		$this->data = $_GET;
		$this->data = array_merge($this->data, $_POST);

		$post_data = [];
		parse_str(file_get_contents("php://input"), $post_data);

		$this->data = array_merge($this->data, $post_data);
		$this->data = array_merge($this->data, $_FILES);
		$this->data = array_merge($this->data, $_COOKIE);
		$this->data['url'] = current_url();
		$this->data['previous_url'] = $_SERVER['HTTP_REFERER'] ?? '';
	}

	public function __debugInfo(){
		return $this->data ?? NULL;
	}

	public function __get($key){
		return $this->data[$key] ?? NULL;
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
		if(!$this->hasFile($name))
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
		return $this->data;
	}

	public function cookie($param){
		return $_COOKIE[$param];
	}

	public function validate(array $rules, array $message = array()){
		validate($this->data, $rules, $message);
	}
}


 ?>
