<?php
namespace System\Supporters\Facades;
use System\App\App;
abstract class Facade
{

	abstract protected static function realClassName();
	abstract protected static function isSingleton();

	public static function __callStatic($method, $args)
	{
		return (new static)->$method(...$args);
	}

	public function __call($method, $args){
		$object = static::getObject(static::realClassName());
		return $object->$method(...$args);
	}

	public function __get($property)
	{
		$object = static::getObject(static::realClassName());
		return $object->$property;
	}

	public static function getInstance(){
		if(static::realClassName() === App::class){
			return App::getInstance();
		}

		return static::getObject(static::realClassName());
	}

	private static function getObject($className){
		if(static::isSingleton()){
			return app()->singleton($className)->resovle($className);
		}else{
			return app()->boot($className, $className)->resovle($className);
		}
	}
}

 ?>
