<?php
namespace System\database\Sqls\builders;

/**
 * JoinBuilder
 */
class JoinBuilder
{
	private $table;
	private $on;
	function __construct($type, $table)
	{
		$this->type = $type;
		$this->table = $table;

		return $this;
	}

	public function on()
	{
		if(is_callable(func_get_args()[0])){
			$this->groupStart('AND');
			$callable = func_get_args()[0];
			$callable($this);
			$this->groupEnd();
		}
		elseif(func_num_args() == 2)
			$this->onHandle('AND', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->onHandle('AND', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method andWhere()", 1);
			die();
		}

		return $this;
	}

	public function orOn()
	{
		if(func_num_args() == 1){
			if(is_callable(func_get_args()[0])){
				$this->groupStart('OR');
				$callable = func_get_args()[0];
				$callable($this);
				$this->groupEnd();
			}
			return $this;
		}
		elseif(func_num_args() == 2)
			$this->onHandle('OR', func_get_args()[0], '=', func_get_args()[1]);
		elseif (func_num_args() >= 3 && func_num_args() <= 5) {
			$this->onHandle('OR', ...func_get_args());
		}else{
			throw new AppException("Number of parameters is not valid for method andWhere()", 1);
			die();
		}

		return $this;
	}

	private function onHandle($logic, $column, $operator, $param1, $param2 = NULL)
	{
		if(strpos($this->on, 'ON') === false){
			$this->on = 'ON'.' ';
		}else if($this->on[strlen($this->on) - 2] != '('){
			$this->on .= $logic.' ';
		}

		if(strpos(strtoupper($operator), 'BETWEEN') !== false){
			$this->on .= '\''.$column.'\' '.strtoupper($operator).' \''.$param1.'\' AND \''.$param2.'\' ';
		}elseif(strpos(strtoupper($operator), 'IN') !== false){
			$this->on .= '\''.$column.'\' '.strtoupper($operator).' ( '.implode($param1, ', ').' ) ';
		}else{
			$this->on .= '\''.$column.'\' '.strtoupper($operator).' \''.$param1.'\' ';
		}

		return $this;
	}

	public function groupStart($logic = 'AND')
	{
		if(strpos($this->on, 'ON') === false){
			$this->on = 'ON ';
		}else{
			$this->on .= strtoupper($logic).' ';
		}
		$this->on .= '( ';
		return $this;
	}

	public function groupEnd()
	{
		$this->on .= ') ';
		return $this;
	}

	public function __toString()
	{
		return trim($this->type.' JOIN '.$this->table.' '.$this->on);
	}
}

 ?>
