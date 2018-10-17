<?php
namespace App\Controllers;
use system\supporters\Controller;
use system\requests\Request;

class TestController extends Controller
{
	public function index(Request $request)
	{
		return $request;
	}
}

?>
