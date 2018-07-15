<?php
namespace system\app;
use \Exception;

/**
 * Exception Handler
 */
class AppException extends Exception
{

	protected $finalMessage = '';
	function __construct($message, $code = null)
	{
		set_exception_handler([$this, 'errorHandle']);
		parent::__construct($message, $code);
		restore_exception_handler();
	}

	public function errorHandle($e){
		ob_start();
		echo view('exception/index', [
			'traces' => $e->getTrace(),
			'message' => $e->getMessage(),
			'code' => $e->getCode()
		]);
		$this->finalMessage = ob_get_contents();
		ob_end_clean();
		echo $this->finalMessage;
		return true;
	}
}


 ?>
