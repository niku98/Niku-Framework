<?php
namespace system\app;


/**
 *
 */
trait HasLoader
{
	/**
	 * Content Singleton
	 *
	 * @var array
	 */
	private $singletons = [];

	/**
	 * Content all boot class info
	 *
	 * @var array
	 */
	private $classes = [];

	public function boot($name, $class, $singleton = false)
	{
		$this->classes[$name] = [
			'class' => $class,
			'singleton' => $singleton,
		];

		return $this;
	}

	public function singleton(string $name, $class = NULL){
		if(count(func_get_args()) === 2){
			$args = is_array($class) ? $class : $args;
			$class = $name;


		}elseif (count(func_get_args()) === 1) {
			$class = $name;
		}elseif(count(func_get_args()) !== 3){
			throw new AppException(App::class."::singleton() has max 3 parameters!");
		}

		return $this->boot($name, $class, true);
	}

	public function resovle(string $name, array $args = array()){
		$classInfo = $this->classes[$name];

		if($classInfo['singleton'] === true){
			if(empty($this->singletons[$name])){
				$this->singletons[$name] = AppLoader::getObject($name);
			}
			return $this->singletons[$name];
		}

		return AppLoader::getObject($classInfo['class'], $args);
	}

	public function make(string $name, $class = NULL, array $args = array())
	{
		if(count(func_get_args()) === 2){
			$args = is_array($class) ? $class : $args;
			$class = is_string($class) ? $class : $name;
		}elseif (count(func_get_args()) === 1) {
			$class = $name;
		}elseif(count(func_get_args()) !== 3){
			throw new AppException(App::class."::make() has max 3 parameters!");
		}
		return $this->boot($name, $class)->resovle($name, $args);
	}
}


 ?>
