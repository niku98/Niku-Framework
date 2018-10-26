<?php
/**
* list all routes in this file
*/

use System\database\Migration\Schema;
use System\Requests\Request;

Route::get('', function(){
	return view('ad.tt');
});

Route::get('test', function(Request $request){
	return view('test');
});

Route::post('test', 'TestController@index');

?>
