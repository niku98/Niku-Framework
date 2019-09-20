<?php
namespace System\Console;

/**
 *
 */
class CreateFile
{
	public static function model($name){
		$path = __DIR__.'/../../app/'.$name.'.php';
		$name_parts = preg_split('/(?=[A-Z])/',$name);
		unset($name_parts[0]);
		$table_name = implode($name_parts, '_') . 's';
		$content = '<?php
namespace App;
use System\Model\Model;


class '.$name.' extends Model
{
	protected $table = \''.strtolower($table_name).'\'; // Table Name
	protected $primaryKey = \'id\'; // Primary key in Table
	protected $properties = [\'something\']; // Another properties in Table
}

?>';
		file_put_contents($path, $content);
		echo "Model $name created!";
	}

	public static function controller($name){
		$path = __DIR__.'/../../app/Controllers/'.$name.'.php';
		$content = '<?php
namespace App\Controllers;
use System\Supporters\Controller;
use System\Requests\Request;

class '.$name.' extends Controller
{

}

?>';
		file_put_contents($path, $content);
		echo "Controller $name created!";
	}

	public static function middleware($name){
		$path = __DIR__.'/../../app/Middlewares/'.$name.'.php';
		$content = '<?php
namespace App\Middlewares;
use System\middlewares\BaseMiddleware;
use System\Requests\Request;

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
