<?php
namespace System\Route\Exception;

/**
 *
 */
class NotFoundException extends HttpException
{
	protected function statusCode()
	{
		return 404;
	}

	protected function message()
	{
		return '404, Page Not Found!';
	}

	protected function title()
	{
		return 'Page Not Found!';
	}
}


 ?>
