<?php

use system\app\App;
use system\app\AppException;
use system\supporters\Session;
use system\supporters\View;
use system\supporters\Redirect;
use system\supporters\Response;
use system\supporters\Validator;
use system\supporters\Lang;

function url(string $uPath = '')
{
	// output: /myproject/index.php
	$currentPath = $_SERVER['PHP_SELF'];

	// output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index )
	$path = pathinfo($currentPath)['dirname'] == '\\' ? '' : pathinfo($currentPath)['dirname'];

	// output: localhost
	$hostName = $_SERVER['HTTP_HOST'];

	// output: http://
	$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';

	$uPath = ltrim($uPath, '/');

	// return: http://localhost/myproject/
	return $protocol.$hostName.$path.'/'.$uPath;
}

function app(){
	return App::getInstance();
}

function route()
{
	$params = func_get_args();
	$route = RouteProcess::find($params[0]);

	if(count($params) > 1){
		unset($params[0]);
		return $route->makeUrl(...$params);
	}

	return $route;
}

function view($viewName,  $data = array()){
	if(!is_string($viewName))
		return;

	$partss = explode('/', $viewName);
	if(count($partss) == 1){
		array_unshift($partss, '');
	}else{
		$folder = '';
		while(count($partss) != 1){
			$folder .= $partss[0].'/';
			array_shift($partss);
		}
		array_unshift($partss, trim($folder, '/'));
	}
	if(!empty($data))
		array_push($partss, $data);
	return new View(...$partss);
}

function redirect(string $url = ''){
	return Redirect::getInstance()->to($url);
}

function response(){
	return Response::getInstance();
}

function validate(array $data_to_check = array(), array $rules = array(), array $custom_messages = array()){
	return Validator::getInstance($data_to_check, $rules, $custom_messages);
}

function session(string $name){
	return Session::get($name);
}

function lang(){
	return Lang::getInstance();
}

function __(){

}

/*------------------------------
EXTENSION FUNCTIONS
------------------------------*/
function rand_token($lenght = 32){
	$source = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$key = '';
	for($i = 0; $i < $lenght; $i++){
		$key .= $source[rand(0, strlen($source) - 1)];
	}
	return $key;
}

function hex_encode($string){
    $hex='';
    for ($i=0; $i < strlen($string); $i++){
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;
}


function hex_decode($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}

function csrf_token()
{
	return session('csrf_token');
}

function add_script($scriptName){
	?>
	<script type="text/javascript" src="<?php echo url('public/js/'.$scriptName.'.js') ?>"></script>
	<?php
}

function add_style($styleName){
	?>
	<link rel="stylesheet" href="<?php echo url('public/css/'.$styleName.'.css') ?>">
	<?php
}

function curl($url, array $data = array()){
	$ch = @curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    $head[] = "Connection: keep-alive";
    $head[] = "Keep-Alive: 300";
	$head[] = 'User-Agent:'.$_SERVER['HTTP_USER_AGENT'];

	curl_setopt($ch, CURLOPT_POST, count($data));
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 130);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

	$html = curl_exec($ch);
	curl_close($ch);
	return $html;
}

function loadFile($path){
	if(file_exists(__DIR__.'/../../'.$path))
		require_once __DIR__.'/../../'.$path;
}

function isRegularExpression($string) {
	set_error_handler(function() {

	}, E_WARNING);
	$isRegularExpression = preg_match($string, "") !== FALSE;
	restore_error_handler();
	return $isRegularExpression;
}

function current_url(){
	return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function scriptEscape(string $html){
	return preg_replace('#<s(.*?)c(.*?)r(.*?)i(.*?)p(.*?)t>(.*?)</s(.*?)c(.*?)r(.*?)i(.*?)p(.*?)t>#is', '', $html);
}

function eHandler($errno, $message, $file, $line){
	if (!(error_reporting() & $errno)) {
		// This error code is not included in error_reporting, so let it fall
		// through to the standard PHP error handler
		return false;
	}


	switch ($errno) {
		case E_ERROR:
		$message = "<b>ERROR:</b> ".$message;
		break;

		case E_CORE_ERROR:
		$message = "<b>CORE ERROR:</b> ".$message;
		break;

		case E_COMPILE_ERROR:
		$message = "<b>COMPILE ERROR:</b> ".$message;
		break;

		case E_USER_ERROR:
		$message = "<b>ERROR:</b> ".$message;
		break;

		case E_RECOVERABLE_ERROR:
		$message = "<b>RECOVERABLE ERROR:</b> ".$message;
		break;

		case E_CORE_WARNING:
		$message = "<b>CORE WARNING:</b> ".$message;
		break;

		case E_WARNING:
		$message = "<b>WARNING:</b> ".$message;
		break;

		case E_COMPILE_WARNING:
		$message = "<b>COMPILE WARNING:</b> ".$message;
		break;

		case E_PARSE:
		$message = "<b>PARSE ERROR:</b> ".$message;
		break;

		case E_NOTICE:
		$message = "<b>NOTICE:</b> ".$message;
		break;
		case E_DEPRECATED:
		$message = "<b>DEPRECATED ERROR:</b> $message";
		break;
		default:
		$message = $errno.' '.$message;
		break;
	}

	$traces = array(
		array(
			'file' => $file,
			'line' => $line
		)
	);

	if(strpos($message, 'Stack trace:') !== false){
		$processingPart = explode('Stack trace:', $message)[1];
		$message = explode('Stack trace:', $message)[0];

		$tracess = explode("\n", $processingPart);
		array_shift($tracess);
		array_pop($tracess);
		array_pop($tracess);
		foreach ($tracess as $trace){
			$trace = explode(' ', $trace)[1];
			$trace = trim($trace, ':');
			preg_match('/\(([\d]+)\)/', $trace, $number);
			$traceLine = $number[1];

			$traceFile = explode('(', $trace)[0];

			$traces[] = array(
				'file' => $traceFile,
				'line' => $traceLine
			);
		}
	}

	$data = [
		'message' => $message,
		'traces' => $traces
	];
	ob_clean();
	echo view('exception/index', $data);
	die();
}

function shutDownHandler(){
	$lasterror = error_get_last();

	if (!(error_reporting() & $lasterror['type'])) {
		// This error code is not included in error_reporting, so let it fall
		// through to the standard PHP error handler
		return false;
	}

	switch ($lasterror['type'])
	{
		case E_ERROR:
		$message = "<b>ERROR:</b> ".$lasterror['message'];
		break;

		case E_CORE_ERROR:
		$message = "<b>CORE ERROR:</b> ".$lasterror['message'];
		break;

		case E_COMPILE_ERROR:
		$message = "<b>COMPILE ERROR:</b> ".$lasterror['message'];
		break;

		case E_USER_ERROR:
		$message = "<b>ERROR:</b> ".$lasterror['message'];
		break;

		case E_RECOVERABLE_ERROR:
		$message = "<b>RECOVERABLE ERROR:</b> ".$lasterror['message'];
		break;

		case E_CORE_WARNING:
		$message = "<b>CORE WARNING:</b> ".$lasterror['message'];
		break;

		case E_WARNING:
		$message = "<b>WARNING:</b> ".$lasterror['message'];
		break;

		case E_COMPILE_WARNING:
		$message = "<b>COMPILE WARNING:</b> ".$lasterror['message'];
		break;

		case E_PARSE:
		$message = "<b>PARSE ERROR:</b> ".$lasterror['message'];
		break;

		case E_NOTICE:
		$message = "<b>NOTICE:</b> ".$lasterror['message'];
		break;
		default:
		$message = $lasterror['type'].' '.$lasterror['message'];
		break;
	}
	$traces = array(
		array(
			'file' => $lasterror['file'],
			'line' => $lasterror['line']
		)
	);

	if(strpos($message, 'Stack trace:') !== false){
		$processingPart = explode('Stack trace:', $message)[1];
		$message = explode('Stack trace:', $message)[0];

		$tracess = explode("\n", $processingPart);
		array_shift($tracess);
		array_pop($tracess);
		array_pop($tracess);
		foreach ($tracess as $trace){
			$trace = explode(' ', $trace)[1];
			$trace = trim($trace, ':');
			preg_match('/\(([\d]+)\)/', $trace, $number);
			$traceLine = $number[1];

			$traceFile = explode('(', $trace)[0];

			$traces[] = array(
				'file' => $traceFile,
				'line' => $traceLine
			);
		}
	}

	$data = [
		'message' => $message,
		'traces' => $traces
	];
	ob_clean();
	echo view('exception/index', $data);
	die();
	return true;
}

?>
