<?php
session_start();

define('root_path', __DIR__.'/');
define('system_path', root_path.'system/');
define('base_folder_name', dirname(__DIR__));

require root_path.'autoload.php';

app()->run();

?>
