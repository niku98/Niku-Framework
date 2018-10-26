<?php
namespace System\View;
use AppException;
use System\Supporters\DotPath;
/**
 * View Class to get view for controller
 */
class View
{
	protected $path;
	protected $layout;
	private $data = array();
	private $deleteAfterRun = false;


	public function __construct(string $path, array $data = array())
	{
		$this->path = $path;
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
		$path = DotPath::findFile(root_path.trim($this->base_path), $this->path, ['niku.php', 'php']);

		if(!$path){
			throw new AppException('File path ['.$this->path.'] not found!');
		}

		if(strpos($path['file'], 'niku.php') === false){
			return $path['file'];
		}

		$this->deleteAfterRun = true;

		return (new NikuTemplate($path['file']))->convert()->save()->getSavedPath();
	}

	public function setBasePath(string $base_path){
		$this->base_path = $base_path;
		return $this;
	}

	public function setLayout(string $path, array $data = array()){
		if(!empty($data)){
			$this->data = $data;
		}
		$this->path = $path;
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
