<?php
use system\view\View;
use system\supporters\Validator;
use system\supporters\Lang;

/*-------------------------------
URL AND PATH
-------------------------------*/
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

	$uPath = trim($uPath, '/');

	return $uPath != '' ? $protocol.$hostName.$path.'/'.$uPath : $protocol.$hostName.$path;
}

function asset($path)
{
	return url(trim($path, '/'));
}

function current_url(){
	return Request::url();
}

/*--------------------------
QUICK FUNCTIONS
--------------------------*/
function app(){
	return App::getInstance();
}

function route($name, array $data = array())
{
	$route = Route::find($name);

	return $route->makeUrl($data);
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
	return Redirect::to($url);
}

function response(){
	return Response::getInstance();
}

function validate(array $data_to_check = array(), array $rules = array(), array $custom_messages = array()){
	return new Validator($data_to_check, $rules, $custom_messages);
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
	<script type="text/javascript" src="<?php echo asset('js/'.$scriptName.'.js') ?>"></script>
	<?php
}

function add_style($styleName){
	?>
	<link rel="stylesheet" href="<?php echo asset('css/'.$styleName.'.css') ?>">
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

if (!function_exists('getallheaders')) {
    function getallheaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
    }
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

function scriptEscape(string $html){
	return preg_replace('#<s(.*?)c(.*?)r(.*?)i(.*?)p(.*?)t>(.*?)</s(.*?)c(.*?)r(.*?)i(.*?)p(.*?)t>#is', '', $html);
}

function reArrayFiles($file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    foreach ($file_post['name'] as $name) {
        foreach ($file_keys as $key) {
            $file_ary[$name][$key] = $file_post[$key][$name];
        }
    }

    return $file_ary;
}

?>
