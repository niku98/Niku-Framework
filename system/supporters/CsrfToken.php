<?php

namespace system\supporters;

/**
 * Csrf Token class
 */
class CsrfToken
{
	public static function generateToken(){
		global $_CONFIG;
		$numRand = rand(12, 34);

		for($i = 0; $i < $numRand; $i += rand(2, 10)){
			if($i % 2 == 0){
				$token = md5(rand_token($numRand)).md5($_CONFIG['NK_APP_TOKEN']);
			}elseif($i % 3 == 0){
				$token = md5($_CONFIG['NK_APP_TOKEN']).md5(rand_token($numRand));
			}else{
				$token = md5(rand_token($numRand / 2)).md5($_CONFIG['NK_APP_TOKEN']).md5(rand_token($numRand / 2));
			}
		}

		return $token;
	}

	public static function checkToken($token){
		global $_CONFIG;

		$md5AppToken = md5($_CONFIG['NK_APP_TOKEN']);

		if(strpos($token, $md5AppToken) !== false)
			return true;

		return false;
	}
}


 ?>
