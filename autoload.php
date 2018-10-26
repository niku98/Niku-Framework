<?php
require_once __DIR__.'/system/NkAutoLoad.php';
NkAutoLoad::getLoader(__DIR__);

NkAutoLoad::file([
	'system/Supporters/helpers'
]);

NkAutoLoad::namespace([
	'MongoDB' => 'system/Database/source_php_libs/mongodb/src',
	'App' => 'app',
	'System' => 'system'
]);

$aliases = (require __DIR__.'/config/app.php')['alias'];

NkAutoLoad::alias($aliases);

 ?>
