<?php
use system\supporters\Request;
use system\supporters\Redirect;
use system\supporters\Response;

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

	private function is_callable(){
		return is_callable($this->action);
	}

	private function getActionParameters(){
		if($this->is_callable()){
			return $this->getFunctionParameters();
		}

		return $this->getMethodParameters();
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
		$request = new Request();

		$actionParameters = $this->getActionParameters();

		$data = array();

		if(count($actionParameters) > 0){ // If method has parameters, pass some data from get method
			for ($i = 0; $i < count($actionParameters); $i++) {
				$type = $actionParameters[$i]->getType();
				$name = $actionParameters[$i]->getName();
				if(class_exists((string)$type)){
					$class = (string)$type;
					$data[] = new $class();
				}else{
					$data[] = $request->get($name);
				}
			}
		}

		return $data;
	}

	private function showActionOutPut($output){
		if($output != NULL){
			if( ( !is_array( $output ) ) && ( ( !is_object( $output ) && settype( $output, 'string' ) !== false ) ||
			( is_object( $output ) && method_exists( $output, '__toString' ) ) ) ){
				echo response()->body($output);
			}
			elseif(!is_array($output)){
				if(strpos(get_class($output), 'Redirect') !== false)
					$output->go();
				elseif(strpos(get_class($output), 'Response') !== false){
					echo $output;
				}else{
					ob_start();

					echo '<pre>';
					var_dump($output);
					echo '</pre>';

					$body = ob_get_clean();
					echo response()->body($body);
				}
			}else{
				ob_start();

				echo '<pre>';
				var_dump($output);
				echo '</pre>';

				$body = ob_get_clean();
				echo response()->body($body);
			}
		}
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
		$class = 'controllers\\'.explode('@', $this->action)[0];
		$action = explode('@', $this->action)[1];

		return [
			'class' => new $class(),
			'method' => $action
		];
	}
}


 ?>
