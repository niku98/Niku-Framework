<?php
require_once __DIR__.'/system/NkAutoLoad.php';
NkAutoLoad::getLoader(__DIR__);

NkAutoLoad::file([
	'system/supporters/helpers'
]);

NkAutoLoad::namespace([
	'MongoDB' => 'system/database/source_php_libs/mongodb/src',
	'App' => 'app'
]);

$aliases = (require __DIR__.'/config/app.php')['alias'];

NkAutoLoad::alias($aliases);

 ?>
