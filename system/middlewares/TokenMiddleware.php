<?php

namespace system\middlewares;
use system\app\AppException;
use system\supporters\Request;
use system\supporters\Session;
use system\supporters\CsrfToken;

/**
 * Middleware
 */
class TokenMiddleware extends BaseMiddleware
{

	public static function handle(){
		if($_SERVER['REQUEST_METHOD'] != 'GET'){
			$request = new Request();
			if(!$request->has('csrf_token')){
				$message = 'You are missing Token!';
			}

			elseif(CsrfToken::checkToken($request->csrf_token) === false){
				$message = 'Your Token is not from this Website!';
			}

			elseif($request->csrf_token !== Session::get('csrf_token')){
				$message = 'Your Token has been expired!';
			}

			if(!empty($message)){
				echo response()->status(400)->body(view('errors/token_error', ['message' => $message]));
				die();
			}
		}
	}
}



 ?>
