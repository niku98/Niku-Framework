<?php
namespace system\supporters;
use system\patterns\Singleton;
use system\supporters\Auth;
use system\supporters\CsrfToken;

/**
 * Session
 */
class Session extends Singleton
{

	public function firstHandle()
	{
		if(self::is_expired()){
			self::reset_id(true);
			self::reset_expire_time();
			Auth::logout();
			self::set('csrf_token', CsrfToken::generateToken());
		}else{
			self::reset_expire_time();
		}
	}

	public function lastHandle()
	{
		self::clearFlashes();
	}

	public function is_expired()
	{
		$_SESSION['_NK_SESSION_EXPIRE_TIME'] = !empty($_SESSION['_NK_SESSION_EXPIRE_TIME']) ? $_SESSION['_NK_SESSION_EXPIRE_TIME'] : 0;
		return $_SESSION['_NK_SESSION_EXPIRE_TIME'] < time();
	}

	public function reset_expire_time(){
		global $_CONFIG;
		$_SESSION['_NK_SESSION_EXPIRE_TIME'] = time() + $_CONFIG['SESSION_EXPIRE_TIME'];
	}

	public function get_id(){
		return session_id();
	}

	public function set_id($id){
		return session_id($id);
	}

	public function reset_id($delete_old = true){
		return session_regenerate_id($delete_old);
	}

	public function destroy()
	{
		return session_destroy();
	}

	public function set(string $key, $value){
		$_SESSION[$key] = $value;
	}

	public function flash(string $key, $value){
		$_SESSION[$key] = $value;
		$_SESSION['NK_FLASH_SESSION_KEY'][] = $key;
	}

	public function clearFlashes(){
		if(empty($_SESSION['NK_FLASH_SESSION_KEY']))
			return;

		foreach ($_SESSION['NK_FLASH_SESSION_KEY'] as $name) {
			self::delete($name);
		}
	}

	public function get($key){
		return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
	}

	public function delete($key='')
	{
		unset($_SESSION[$key]);
	}
}


 ?>
