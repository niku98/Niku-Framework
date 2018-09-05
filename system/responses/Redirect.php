<?php
namespace system\responses;
use system\patterns\Singleton;
use Session;
use \Request;

/**
 * Redirect Class
 */
class Redirect extends Singleton
{
	private $url;
	private $code;
	private $flashData;

	public function __construct()
	{

	}

	public function to($url){
		if(preg_match('/^(http|https):\\/\\/[a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.[a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i', $url, $matched) !== false){
			$this->url = url($url);
		}else{
			$this->url = $url;
		}
		return $this;
	}

	public function route(){
		$this->url = route(...func_get_args());
		return $this;
	}

	public function refresh()
	{
		$this->url = current_url();
		return $this;
	}

	public function back(){
		$this->url = Request::previous_url();
		return $this;
	}

	public function withCode($code){
		$this->code = $code;
		return $this;
	}

	public function getUrl(){
		return $this->url;
	}

	public function with(string $name, $value){
		Session::flash($name, $value);

		return $this;
	}

	public function go(){
		if(empty($this->code))
			header('location: '.$this->url);
		else{
			header('location: '.$this->url, true, $this->code);
		}
		die();
	}
}



 ?>
