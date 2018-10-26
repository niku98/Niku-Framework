<?php

namespace System\Console;

/**
 *
 */
class Console
{
	private static $listAction = [];
	private $curAction = '';

	function __construct()
	{
		global $argv;
		global $argc;
		if($argc <= 1){
			echo "No action run!";
			return;
		}

		$this->parameters = $argv;

		array_shift($this->parameters);
		array_shift($this->parameters);

		$this->curAction = $argv[1];
	}

	public function command(string $name, $action){
		self::$listAction[] = array(
			'name' => $name,
			'action' => $action
		);
	}

	public function run(){
		loadFile('system/console/base.php');
		loadFile('routes/console.php');
		foreach (self::$listAction as $action) {
			if($action['name'] == $this->curAction){
				$action['action'](...$this->parameters);
				break;
			}
		}
	}
}


 ?>
