<?php
namespace system\console;

/**
 *
 */
class CreateFile
{
	public static function model($name){
		$path = __DIR__.'/../../models/'.$name.'.php';
		$content = '<?php
namespace models;
use system\model\Model;


class '.$name.' extends Model
{
	protected $table = \''.strtolower($name).'\'; // Table Name
	protected $identification = \'id\'; // Primary key in Table
	protected $properties = [\'something\']; // Another properties in Table
}

?>';
		file_put_contents($path, $content);
		echo "Model $name created!";
	}

	public function controller($name){
		$path = __DIR__.'/../../controllers/'.$name.'.php';
		$content = '<?php
namespace controllers;
use system\supporters\Controller;
use system\supporters\Request;

class '.$name.' extends Controller
{

}

?>';
		file_put_contents($path, $content);
		echo "Controller $name created!";
	}

	public function middleware($name){
		$path = __DIR__.'/../../middlewares/'.$name.'.php';
		$content = '<?php
namespace middlewares;
use system\middlewares\BaseMiddleware;
use system\app\AppException;
use system\supporters\Request;

class '.$name.' extends BaseMiddleware
{
	public static function handle(){

	}
}

?>';
		file_put_contents($path, $content);
		echo "Middleware $name created!";
	}
}



 ?>
