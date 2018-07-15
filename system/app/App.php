<?php
namespace system\app;
use system\patterns\Singleton;
use system\app\AppException;
use system\middlewares\TokenMiddleware;
use system\supporters\Session;

/**
 * App class
 */
class App extends Singleton
{
	private $locale = '';

	protected function __construct(){
		global $_CONFIG;
		$this->locale = $_CONFIG['NK_LOCALE'];
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
		$uri = \RouteProcess::getRequestUrl();

		if( $uri !== '' && strpos($uri, 'index.') !== false){
			return redirect('');
		}

		Session::firstHandle();

		if(strpos($uri, 'api') === 0){
			\RoutePrefix::getInstance()->url = 'api';
			loadFile('routes/api.php');
		}else{
			loadFile('routes/web.php');
			TokenMiddleware::handle();
		}


		if(!\RouteProcess::map()){
			return response()->status(404)->prepare()->body(view('errors/404'));
		}
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
		global $_CONFIG;
		error_reporting(E_ERROR | E_CORE_ERROR | E_PARSE | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_USER_ERROR | E_RECOVERABLE_ERROR | E_NOTICE | E_WARNING);
		if($_CONFIG['NK_ERROR_REPORTING'] == true){
			ini_set('display_errors',0);
			set_error_handler('eHandler');
			register_shutdown_function("shutDownHandler");
		}
	}
}


 ?>
