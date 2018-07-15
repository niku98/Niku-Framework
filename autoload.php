<?php

$_NK_LIST_AUTOLOAD = [
	'system/route/Route',
	'system/route/RouteProcess',
	'system/route/RoutePrefix',
	'system/route/RouteAction',
	'system/supporters/library'
];

$_NK_LIST_NAMESPACE = [
	'MongoDB' => 'system/database/source_php_libs/mongodb/src',
];

foreach ($_NK_LIST_AUTOLOAD as $class) {
	require_once __DIR__.'/'.$class.'.php';
}

function __autoload($className){
	global $_NK_LIST_NAMESPACE;
	$checkNameSpace = 0;

	foreach ($_NK_LIST_NAMESPACE as $namespace => $source) {
		if(strpos($className, $namespace) === 0){
			$checkNameSpace = 1;

			$className = $source.'/'.ltrim($className, $namespace);
			break;
		}
	}

	$className = explode('\\', $className);
	$className = implode($className, '/');

	if(file_exists(__DIR__.'/'.$className.'.php')){
		require_once __DIR__.'/'.$className.'.php';
	}
}

 ?>
