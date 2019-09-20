<?php
namespace System\App;
use System\Patterns\Singleton;
use System\App\AppException;
use System\middlewares\TokenMiddleware;
use Session;
use System\App\Exception\ErrorHandler;
use System\Supporters\EnvReader;

/**
 * App class
 */
class App
{
	use HasLoader;

	private $locale = '';
	private $config;
	private static $instance;
	private $env;

	private function __construct(){
		$this->config = require root_path.'config/app.php';
		$this->env = new EnvReader(root_path.'.env');

		$this->locale = $this->env['NK_LOCALE'];
	}

	public static function getInstance()
	{
		if(!self::$instance){
			self::$instance = new static;
		}

		return self::$instance;
	}

	public function config($key){
		return $this->config[$key] ?? NULL;
	}

	public function env($name, $default = null)
	{
		return $this->env->has($name) ? $this->env[$name] : $default;
	}

	public function locale(string $locale = ''){
		if($locale == '')
			return $this->locale;

		if(is_string($locale))
			$this->locale = $locale;

		return $this;
	}

	public function handle()
	{
		$uri = \Route::getRequestUrl();

		if( $uri !== '' && strpos($uri, 'index.') !== false){
			return redirect()->to('');
		}

		Session::firstHandle();

		\Route::map();

		Session::lastHandle();
	}

	public function run(){
		$this->registerErrorHandler();
		$result = $this->handle();
		if(is_object($result)){
			if(strpos(get_class($result), 'Redirect') !== false){
				$result->go();
			}elseif(strpos(get_class($result), 'Response') !== false){
				echo $result->getBody();
			}
		}
	}

	protected function registerErrorHandler(){
		ErrorHandler::getHandler();
	}
}


 ?>
