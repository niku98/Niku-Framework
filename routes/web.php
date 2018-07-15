<?php
/**
* list all routes in this file
*/
use system\database\Database;
use system\supporters\Request;

Route::get('', function(){
	return models\User::pagination(25);
});

Route::prefix(['url' => 'url', 'middlewares' => function(){return true;}], function(){
	Route::prefix(['url' => 'url2', 'middlewares' => ['TestMiddleware']], function(){
		Route::get('2', function(){
			return "something";
		});
	});
	Route::get('1', function(){
		return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	});
});

Route::post('post-process', 'TestController@create');

Route::get('404', 'ErrorController@notFound');
Route::get('403', 'ErrorController@forbidden');

?>
