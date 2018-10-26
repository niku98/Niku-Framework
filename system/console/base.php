<?php
use System\Console\Console;
use System\Console\CreateFile;

Console::command('create', function($type, $name){
	CreateFile::$type($name);
});


 ?>
