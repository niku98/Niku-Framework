<?php
namespace system\view;
use AppException;
/**
 * View Class to get view for controller
 */
class View
{
	protected $folder;
	protected $file;
	protected $layout;
	private $data = array();
	private $deleteAfterRun = false;


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

		$path = $this->getConvertedFilePath();

		ob_start();
		require_once($path);
		$this->layout = ob_get_contents();
		ob_end_clean();
		if($this->deleteAfterRun == true){
			unlink($path);
		}
		return $this;
	}

	private function getConvertedFilePath()
	{
		$path_1 = root_path.trim($this->base_path, '/').'/'.$this->folder.'/'.$this->file.'.php';
		if(file_exists($path_1)){
			return $path_1;
		}

		$path_2 = root_path.trim($this->base_path, '/').'/'.$this->folder.'/'.$this->file.'.niku.php';

		if(!file_exists($path_2)){
			throw new AppException('File path ['.root_path.trim($this->base_path, '/').'/'.$this->folder.'/'.$this->file.'] not found!');
		}

		$this->deleteAfterRun = true;

		return (new NikuTemplate($path_2))->convert()->save()->getSavedPath();
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

	public function with(array $data)
	{
		$this->data = $data;
		return $this;
	}

	public function getLayout(){
		$this->getFileLayout();
		return $this->layout;
	}
}

 ?>
