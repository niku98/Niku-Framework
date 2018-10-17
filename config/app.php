<?php

return [
	'alias' => [
		'App' => system\supporters\facades\App::class,
		'AppException' => system\app\exception\AppException::class,
		'Auth' => system\supporters\Auth::class,
		'Database' => system\database\Database::class,
		'Route' => system\route\Route::class,
		'Router' => system\route\Router::class,
		'RoutePrefix' => system\route\RoutePrefix::class,
		'RouteAction' => system\route\RouteAction::class,
		'View' => system\view\View::class,
		'Request' => system\supporters\facades\Request::class,
		'Response' => system\supporters\facades\Response::class,
		'Session' => system\supporters\facades\Session::class,
		'Redirect' => system\supporters\facades\Redirect::class,
	]
];

 ?>
