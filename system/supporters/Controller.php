<?php
namespace System\Supporters;
use Route;

/**
* Base Controller
* Parent Controller, another Controller will extend this Controller
*/
class Controller
{
	public $route;
	public function __construct()
	{
		$this->route = Route::current();
	}
}


?>
