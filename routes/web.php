<?php
/**
* list all routes in this file
*/
use system\supporters\Request;

Route::get('{id}/{name}', function(Request $request){
	return [$id, $name];
})->where([
	'id' => '[\d]+'
]);

Route::get('ok', function(){
	return 'ok';
});

// Don't delete below lines
Route::get('404', 'ErrorController@notFound');
Route::get('403', 'ErrorController@forbidden');

?>
