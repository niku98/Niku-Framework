<?php
namespace system\supporters;
/**
 * View Class to get view for controller
 */
class View
{
	protected $folder;
	protected $file;
	protected $layout;
	private $data = array();


	public function __construct(string $folder, string $file = '',array $data = array())
	{
		$this->folder = $folder;
		$this->file = $file;
		$this->data = $data;
		$this->base_path = app()->config('VIEW_PATH');

		return $this;
	}

	private function getFileLayout(){

		if(!empty($this->data) && is_array($this->data)){
			extract($this->data);
		}

		$path = root_path.trim($this->base_path, '/').'/'.$this->folder.'/'.$this->file.'.php';

		ob_start();
		require_once($path);
		$this->layout = ob_get_contents();
		ob_end_clean();

		return $this;
	}

	public function setBasePath(string $base_path){
		$this->base_path = $base_path;
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
		$this->file = $file_name;
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

		$path = root_path.trim($this->base_path, '/').'/'.$path.'.php';

		ob_start();
		require_once($path);
		$layout = ob_get_contents();
		ob_end_clean();

		return $layout;
	}

	public function with(array $data)
	{
		$this->data = $data;
		return $this;
	}

	public function __toString(){
		$this->getFileLayout();
		return $this->layout;
	}
}

 ?>
