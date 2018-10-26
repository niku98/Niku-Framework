<?php
namespace App\Controllers;
use System\Supporters\Controller;
use System\Supporters\View;
/**
 * Controll errors
 */
class ErrorController extends Controller
{

	function __construct()
	{
		$this->view = new View('errors');
	}

	public function notFound(){
		return $this->showView('404');
	}

	function forbidden()
	{
		return $this->showView('403');
	}
}


 ?>
