<?php
namespace system\route\exception;
/**
* Token
*/
class TokenException extends HttpException
{
	protected function statusCode()
	{
		return 400;
	}

	protected function message()
	{
		return 'The page you are looking for is expired!';
	}

	protected function title()
	{
		return 'Token Error!';
	}
}


?>
