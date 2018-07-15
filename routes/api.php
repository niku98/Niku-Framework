<?php

Route::prefix(['url' => '', 'middlewares' => []], function(){
	Route::any('something', function(){
		echo "string";
	});
});

 ?>
