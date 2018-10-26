<?php
namespace System\App;
use System\patterns\Singleton;
use System\App\AppException;
use System\middlewares\TokenMiddleware;
use Session;
use System\App\Exception\ErrorHandler;

/**
 * App class
 */
class App
{
	use HasLoader;

	private $locale = '';
	private $config;
	private static $instance;

	private function __construct(){
		$this->config = require_once root_path.'/config.php';
		$this->locale = $this->config['NK_LOCALE'];
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
