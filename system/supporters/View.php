<?php
namespace system\supporters;
/**
 * View Class to get view for controller
 */
class View
{
	protected $folder;
	protected $layout;
	private $data = array();


	function __construct(string $folder, string $file = '',array $data = array())
	{
		$this->folder = $folder;
		$this->data = $data;

		if(!empty($file) && is_string($file)){
			$this->getFileLayout($file);
		}
		return $this;
	}

	private function getFileLayout(string $file){

		if(!empty($this->data) && is_array($this->data)){
			extract($this->data);
		}

		$path = root_path.'resources/views/'.$this->folder.'/'.$file.'.php';

		ob_start();
		require_once($path);
		$this->layout = ob_get_contents();
		ob_end_clean();

		return $this;
	}

	public function setFolder(string $folder){
		$this->folder = $folder;
		return $this;
	}

	public function setLayout(string $file_name, array $data = array()){
		if(!empty($data)){
			$this->data = $data;
		}
		$this->getFileLayout($file_name);
		return $this;
	}

	public function getCurrentLayout(){
		return $this->layout;
	}

	public function get(string $file){
		$this->getFileLayout($file);
		return $this;
	}

	public function getPartial(string $path, array $data = array()){
		if(!empty($data) && is_array($data)){
			extract($data);
		}

		$path = root_path.'resources/views/'.$path.'.php';

		ob_start();
		require_once($path);
		$layout = ob_get_contents();
		ob_end_clean();

		return $layout;
	}

	public function __toString(){
		return $this->layout;
	}
}

 ?>
