<?php
use system\console\Console;
use system\console\CreateFile;

Console::command('create', function($type, $name){
	CreateFile::$type($name);
});


 ?>
