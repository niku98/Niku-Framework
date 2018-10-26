<?php
namespace System\App;
use \ReflectionMethod;
use \ReflectionClass;
use Reflector;
use ReflectionFunctionAbstract;

/**
 * Apploader
 */
class AppLoader
{
	/*-------------------------------
	Reflect Object Methods
	-------------------------------*/
	public static function getObject(string $class, array $args = array())
	{
		$reflectionClass = self::getReflectionClass($class);
		$args = static::getClassConstructorParametersData($reflectionClass, $args);

		return $reflectionClass->newInstance(...$args);
	}

	private static function getReflectionClass(string $class)
	{
		$reflection = new ReflectionClass($class);
		return $reflection;
	}

	private static function getClassConstructorParametersData(ReflectionClass $reflectionClass, array $args = array()){
		$reflectionConstructor = $reflectionClass->getConstructor();
		if(is_null($reflectionConstructor)){
			return [];
		}

		$params = self::getParamters($reflectionConstructor);
		$args = self::createData($params, $args);

		return $args;
	}

	/*-------------------------------
	Base Methods
	-------------------------------*/
	private static function getParamters(ReflectionFunctionAbstract $reflection)
	{
		return $reflection->getParameters();
	}

	private static function createData(array $params, array $args = array())
	{
		if(count($params) === count($args)){
			return array_values($args);
		}

		$args = array();

		foreach ($params as $param) {
			$type = $param->getType();
			if(class_exists($type)){
				$args[] = static::getObject($type);
			}
		}

		return $args;
	}
}


 ?>
