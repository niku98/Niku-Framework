<?php

return [
	'alias' => [
		'App' => System\Supporters\Facades\App::class,
		'AppException' => System\App\Exception\AppException::class,
		'Auth' => System\Supporters\Auth::class,
		'Database' => System\database\Database::class,
		'Route' => System\Route\Route::class,
		'Router' => System\Route\Router::class,
		'RoutePrefix' => System\Route\RoutePrefix::class,
		'RouteAction' => System\Route\RouteAction::class,
		'View' => System\View\View::class,
		'Request' => System\Supporters\Facades\Request::class,
		'Response' => System\Supporters\Facades\Response::class,
		'Session' => System\Supporters\Facades\Session::class,
		'Redirect' => System\Supporters\Facades\Redirect::class,
	]
];

 ?>
