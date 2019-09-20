<?php

namespace System\Supporters;

/**
 * Csrf Token class
 */
class CsrfToken
{
	public static function generateToken(){
		$numRand = rand(12, 34);

		for($i = 0; $i < $numRand; $i += rand(2, 10)){
			if($i % 2 == 0){
				$token = md5(rand_token($numRand)).md5(app()->env('NK_APP_TOKEN'));
			}elseif($i % 3 == 0){
				$token = md5(app()->env('NK_APP_TOKEN')).md5(rand_token($numRand));
			}else{
				$token = md5(rand_token($numRand / 2)).md5(app()->env('NK_APP_TOKEN')).md5(rand_token($numRand / 2));
			}
		}

		return $token;
	}

	public static function checkToken($token){
		$md5AppToken = md5(app()->env('NK_APP_TOKEN'));

		if(strpos($token, $md5AppToken) !== false)
			return true;

		return false;
	}
}


 ?>
