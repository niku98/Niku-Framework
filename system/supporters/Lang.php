<?php
namespace system\supporters;
use system\patterns\Singleton;
use system\supporters\DotPath;
use \AppException;

/**
 *
 */
class Lang extends Singleton
{

	/**
	 * Array words from language file
	 *
	 * @var array
	 */
	private $words = [];


	/**
	 * path to language file
	 *
	 * @var string
	 */
	private $path = '';

	private $base = '';


	protected function __construct(){
		$this->base = root_path.'resources/languages/'.app()->locale();

		return $this;
	}

	/**
	 * Public method to load Language file
	 *
	 * @param	  string $path
	 * @return    Lang
	 */
	public function load(string $path){
		if($this->needToLoad($path)){
			$this->setPath($path);
			$this->loadFileLang();
		}

		return $this;
	}


	/**
	 * Set path property
	 *
	 * @param     string $path
	 * @throws	  system\app\AppException
	 * @return    Lang
	 */
	public function setPath($path='')
	{
		$this->path = new DotPath($path, $this->base);;
		return $this;
	}


	/**
	 * Real Method load Language File
	 *
	 * @param     string $path
	 * @throws	  system\app\AppException
	 * @return    Lang
	 */
	private function loadFileLang(){
		if(file_exists($this->path)){
			$this->words = require $this->path;
		}else{
			throw new AppException("Your Language file: \"$this->path\" is not exists!");
		}

		return $this;
	}


	/**
	 * Check if need to load new language file
	 *
	 * @param     string $path
	 * @return    bool
	 */
	private function needToLoad(string $path){
		if($this->path->getPath() != $path)
			return true;

		if($this->words == [])
			return true;
	}


	/**
	 * Magic method to get word from $words
	 *
	 * @param     string $key
	 * @return    string/null
	 */
	public function __get(string $key){
		return $this->words[$key] ?? NULL;
	}


	/**
	 * Method to get word from $words if key has special character
	 *
	 * @param	  string $key
	 * @return    string/null
	 */
	public function get(string $key){
		return $this->words[$key] ?? NULL;
	}
}



 ?>
