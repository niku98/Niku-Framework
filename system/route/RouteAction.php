<?php
namespace System\Route;

use Request;
use Redirect;
use ReflectionMethod;
use ReflectionFunction;
/**
 * Route action
 */
class RouteAction
{

	function __construct($action)
	{
		$this->action = $action;
		return $this;
	}

	public function do(){
		$data = $this->getParametersDataFromRequest();
		$output = $this->getActionResult(...$data);

		$this->showActionOutPut($output);
	}

	private function getActionResult(){
		$params = func_get_args();

		if($this->is_callable()){
			$action = $this->action;
			return $action(...$params);
		}

		$cl = $this->getClassMethod();

		return $cl['class']->{$cl['method']}(...$params);
	}

	private function getParametersDataFromRequest(){

		$actionParameters = $this->getActionParameters();

		$data = array();

		if(count($actionParameters) > 0){ // If method has parameters, pass some data from get method
			for ($i = 0; $i < count($actionParameters); $i++) {
				$type = $actionParameters[$i]->getType();
				$name = $actionParameters[$i]->getName();
				if(class_exists($type)){
					$data[] = app()->make($type);
				}else{
					$data[] = Request::get($name);
				}
			}
		}

		return $data;
	}

	private function getActionParameters(){
		if($this->is_callable()){
			return $this->getFunctionParameters();
		}

		return $this->getMethodParameters();
	}

	private function showActionOutPut($output){
		response()->body($output)->show();
	}

	private function is_callable(){
		return is_callable($this->action);
	}

	private function getFunctionParameters(){
		$t = new ReflectionFunction($this->action);

		return $t->getParameters();
	}

	private function getMethodParameters(){
		$cl = $this->getClassMethod();

		$t = new ReflectionMethod($cl['class'], $cl['method']);

		return $t->getParameters();
	}

	private function getClassMethod(){
		$class = 'App\\Controllers\\'.explode('@', $this->action)[0];
		$action = explode('@', $this->action)[1];

		return [
			'class' => app()->make($class),
			'method' => $action
		];
	}
}


 ?>
