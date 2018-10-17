<?php

class NkAutoLoad
{
	/**
	 * List namespace's path
	 *
	 * @var array
	 */
	private static $namespaces = [];

	/**
	 * Class Instance
	 *
	 * @var NkAutoLoad|null
	 */
	private static $instance;

	/**
	 * List Class Alias
	 *
	 * @var array
	 */
	private static $aliases = [];

	/**
	 * Base project's path
	 *
	 * @var string
	 */
	private static $base_path;

	private function __construct($base_path){
		self::$base_path = $base_path;
	}

	public static function getLoader($base_path){
		if(!self::$instance){
			self::$instance = new static($base_path);
		}
		spl_autoload_register(array(self::$instance, 'loadClass'), true, true);

		return self::$instance;
	}

	/**
	 * Add real namespace path
	 *
	 * @param	  array|string
	 * @param 	  null|string
	 * @return    void
	 */
	public static function namespace(){
		$data = func_get_args();
		if(is_array($data[0])){
			foreach ($data[0] as $namespace => $path) {
				self::$namespaces[$namespace] = $path;
			}
		}else{
			self::$namespaces[$data[0]] = $data[1];
		}
	}

	/**
	 * Add file need to autoload
	 *
	 * @param	  array|string
	 * @param 	  null|string
	 * @return    void
	 */
	public static function file(){
		$data = func_get_args();
		if(is_array($data[0])){
			foreach ($data[0] as $file) {
				self::requireFile($file);
			}
		}else{
			self::requireFile($data[0]);
		}
	}

	public static function alias()
	{
		$data = func_get_args();
		if(is_array($data[0])){
			foreach ($data[0] as $class => $path) {
				class_alias($path, $class);
			}
		}else{
			class_alias($data[0], $data[1]);
		}
	}

	/**
	 * Load Class
	 *
	 * @param	  string $class
	 * @return    void
	 */
	public function loadClass(string $class)
	{
		$path = self::getClassPath($class);
		self::requireFile($path);
	}

	/**
	 * Get real path for namespace
	 *
	 * @param	  string $className
	 * @return    string
	 */
	private static function getClassPath(string $className){

		foreach (self::$namespaces as $namespace => $source) {
			if(strpos($className, $namespace) === 0){
				$className = $source.'\\'.trim(ltrim($className, trim($namespace, '\\')), '\\');
				break;
			}
		}

		$className = explode('\\', $className);
		$className = implode($className, '/');

		return $className;
	}

	/**
	 * Require class file
	 *
	 * @param
	 * @return    void
	 * @author
	 * @copyright
	 */
	private static function requireFile(string $path)
	{
		$file = rtrim(self::$base_path, '/').'/'.trim($path, '/').'.php';
		if(file_exists($file)){
			require_once $file;
		}
	}
}


 ?>
