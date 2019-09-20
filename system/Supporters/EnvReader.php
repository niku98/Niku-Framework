<?php
namespace System\Supporters;
use System\Patterns\Abstracts\NkArrayAccess;
use System\Patterns\Abstracts\HasDataProperty;

/**
 *
 */
class EnvReader extends NkArrayAccess
{
	use HasDataProperty;

	private $file_path;

	function __construct($path)
	{
		$this->file_path = $path;
		$this->loadVariables();
		return $this;
	}

	private function loadVariables(){
		$content = file_get_contents($this->file_path);
		$variables = explode("\n", $content);
		foreach ($variables as $variable) {
			if(empty($variable)){
				continue;
			}
			$parts = explode('=', $variable);
			if(count($parts) == 1){
				continue;
			}
			
			$this->data[$parts[0]] = $this->getRealValue($parts[1]);
		}
	}

	private function getRealValue($value){
		$value = trim($value);
		if(strtolower($value) === 'true'){
			return true;
		}elseif(strtolower($value) === 'false'){
			return false;
		}elseif(is_numeric($value)){
			if(is_integer($value)){
				return (int)$value;
			}else{
				return (float)$value;
			}
		}elseif(strtolower($value) === 'null'){
			return null;
		}

		return $value;
	}
}


 ?>
