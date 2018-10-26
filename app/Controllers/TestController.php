<?php
namespace App\Controllers;
use System\Supporters\Controller;
use System\Requests\Request;

class TestController extends Controller
{
	public function index(Request $request)
	{
		return $request;
	}
}

?>
