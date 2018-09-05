<?php
namespace system\route\exception;
use Exception;
use Request;

class HttpException extends Exception
{
	/**
	 * Exception Viewer to show error message
	 *
	 * @var View
	 */
	protected $viewer;

	/**
	 * Base View Path to get exception view
	 *
	 * @var string
	 */
	protected $baseViewPath = 'system/route/exception/views';


	function __construct()
	{
		parent::__construct(...func_get_args());
		$this->setViewer();
		set_exception_handler(array($this, 'report'));
	}

	public function report()
	{
		ob_clean();
		if(Request::isAjax()){
			echo response()->status($this->statusCode())->json([
				'message' => $this->message(),
			]);
		}else {
			echo response()->status($this->statusCode())->body($this->viewer->with([
				'message' => $this->message(),
				'title' => $this->title(),
			]));
		}
		exit;
	}

	protected function statusCode()
	{
		return 500;
	}

	protected function setViewer()
	{
		$this->viewer = view('index')->setBasePath($this->baseViewPath);
		return $this;
	}
}


 ?>
