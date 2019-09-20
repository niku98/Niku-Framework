<?php
namespace System\App\Authentication;
use App\User;
use Session;
/**
 * Authentication Class
 */
class Auth
{
	private static $user;

	public static function login($username, $password){
		$user = User::findBy('username', $username);
		if(!$user){
			return false;
		}

		$password = md5($password).md5($user->salt_token);
		if($user->password !== $password)
			return false;

		Session::reset_expire_time();
		Session::set('_NK_AUTH', $user->id);
		return true;
	}

	public static function loginWithUsername(string $username){
		$user = User::findBy('username', $username);
		if(!$user){
			return false;
		}

		Session::reset_expire_time();
		Session::set('_NK_AUTH', $user->id);
		return true;
	}

	public static function loginWithEmail($email, $password){
		$user = User::findBy('email', $email);
		if(!$user){
			return false;
		}

		$password = md5($password).md5($user->salt_token);
		if($user->password !== $password)
			return false;

		Session::reset_expire_time();
		Session::set('_NK_AUTH', $user->id);
		return true;
	}

	public static function check(){
		return !empty(Session::get('_NK_AUTH'));
	}

	public static function logout(){
		Session::delete('_NK_AUTH');
	}

	public static function user()
	{
		if(!self::$user){
			self::$user = User::find(session('_NK_AUTH'));
		}
		return self::$user;
	}


}


 ?>
