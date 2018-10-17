<?php
/**
* list all routes in this file
*/

use system\database\migration\Schema;
use system\requests\Request;

Route::get('', function(){
	return view('index');
});

Route::get('test', function(Request $request){
	return view('test');
});

Route::post('test', 'TestController@index');

// Route::prefix('test', function(){
// 	Route::post('a', function(){
// 		return Request::all();
// 	})->name('test_post');
// });

?>
