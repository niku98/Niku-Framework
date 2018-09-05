<?php
namespace system\app\exception;
use Request;
use View;
/**
*
*/
class ErrorHandler
{
	private $viewer;
	private $baseViewPath = 'system/app/exception';

	private function __construct()
	{
		$this->viewer = (new View('views'))->setBasePath($this->baseViewPath);
	}

	public static function getHandler(){
		ini_set('display_errors', 'On');
		error_reporting( E_ERROR | E_CORE_ERROR | E_PARSE | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_USER_ERROR | E_RECOVERABLE_ERROR | E_NOTICE | E_WARNING | E_DEPRECATED);
		register_shutdown_function(array(new static(), 'handler'));
	}

	public function handler(){
		$error = error_get_last();
		$errno = $error['type'];
		$message = $error['message'];
		$line = $error['line'];
		$file = $error['file'];

		if (!(error_reporting() & $errno)) {
			return false;
		}

		$data = $this->getErrorData($errno, $message, $file, $line);

		$this->showErrorView($data);

		die();
	}

	private function getErrorData($errno, $message, $file, $line)
	{
		$traces = $this->getListTraces($message, $line, $file);
		$message = $this->reWriteMessage($message);

		$data = [
			'message' => $message,
			'traces' => $traces
		];

		return $data;
	}

	private function showErrorView($data)
	{
		ob_clean();
		$this->showErrorViewWithoutReportingIfDebugOff();

		$this->showErrorViewWithReporting($data);
	}

	private function showErrorViewWithoutReportingIfDebugOff()
	{
		if(app()->config('APP_DEBUG') == false){
			$this->viewer->setLayout('error_alert');
			echo response()->status(500)->body($this->viewer);
			die();
		}
	}

	private function showErrorViewWithReporting($data)
	{
		if(Request::isAjax()){
			return $this->showReportingWithJson($data);
		}
		return $this->showReportingWithHtml($data);
	}

	private function showReportingWithJson($data)
	{
		echo response()->status(500)->json($data);
	}

	private function showReportingWithHtml($data)
	{
		echo response()->status(500)->body(
			$this->viewer->setLayout('index', $data)
		);
	}

	private function getListTraces($message, $line, $file)
	{
		$traces = array(
			array(
				'file' => $file,
				'line' => $line
			)
		);

		if(strpos($message, 'Stack trace:') !== false){
			$processingPart = explode('Stack trace:', $message)[1];
			$message = explode('Stack trace:', $message)[0];

			$tracess = explode("\n", $processingPart);
			array_shift($tracess);
			array_pop($tracess);
			array_pop($tracess);
			foreach ($tracess as $trace){
				$trace = explode(' ', $trace)[1];
				$trace = trim($trace, ':');
				preg_match('/\(([\d]+)\)/', $trace, $number);
				$traceLine = $number[1];

				$traceFile = explode('(', $trace)[0];

				$traces[] = array(
					'file' => $traceFile,
					'line' => (int)$traceLine
				);
			}
		}

		return $traces;
	}

	private function reWriteMessage($message)
	{
		if(strpos($message, 'Stack trace:') !== false){
			$message = explode('Stack trace:', $message)[0];
			$message = substr($message, strpos($message, ':') + 1, strpos($message, 'in', -1) - strpos($message, ':'));
		}

		return $message;
	}
}


?>
