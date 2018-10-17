<?php
namespace system\database\migration;

/**
 *
 */
class BluePrintProperty
{
	private $name;
	private $type = 'int';
	private $length = -1;
	private $nullable = false;
	private $default = null;
	private $collation = '';
	private $autoIncrement = false;
	private $check = array();
	private $unique = false;
	private $primary = false;

	private $foreignKey = false;
	private $references;
	private $onTable = '';
	private $onDelete = '';

	function __construct(string $name, string $type, int $length)
	{
		$this->name = $name;
		$this->type = $type;
		$this->length = $length;

		return $this;
	}

	public function __call($method, $params)
	{
		if((strpos($method, 'is') !== false || strpos($method, 'get') !== false) && count($params) === 0){
			return $this->getPropertyFormCall($method);
		}

		if(strpos($method, 'has') !== false && count($params) === 0){
			return $this->checkPropertyExistsFromCall($method);
		}

		if(isset($this->$method) || $this->$method === null){
			if(count($params) === 0){
				$this->$method = true;
			}else{
				if(is_array($this->$method)){
					if(is_array($params[0])){
						array_push($this->$method, ...$params[0]);
					}else{
						array_push($this->$method, ...$params);
					}
				}else{
					$this->$method = $params[0];
				}
			}
			return $this;
		}
	}

	private function getPropertyFormCall($method)
	{
		$method = ltrim($method, 'is');
		$method = ltrim($method, 'get');
		$method = lcfirst($method);
		return $this->$method;
	}

	private function checkPropertyExistsFromCall($method)
	{
		$method = ltrim($method, 'has');
		$method = lcfirst($method);
		return is_array($this->$method) ? count($this->$method) : (isset($this->$method) && $this->$method !== null);
	}
}


 ?>
