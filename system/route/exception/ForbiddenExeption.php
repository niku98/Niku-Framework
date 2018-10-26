<?php
namespace System\Route\Exception;

/**
 *
 */
class ForbiddenException extends HttpException
{
	protected function statusCode()
	{
		return 403;
	}

	protected function message()
	{
		return '403, Forbidden!';
	}

	protected function title()
	{
		return 'Forbidden!';
	}
}


 ?>
