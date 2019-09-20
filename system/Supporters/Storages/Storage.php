<?php
namespace System\Supporters\Storages;

/**
 *
 */
class Storage
{
	private $base_path;


	private function __construct($base_path = '')
	{
		$this->base_path = $base_path;
		return $this;
	}

	private function getRealPath($path)
	{
		return root_path.trim($this->base_path, '/').'/'.trim($path, '/');
	}

	public static function folder($path)
	{
		return new static($path);
	}

	public function has($path)
	{
		return $this->hasDir($path) || $this->hasFile($path);
	}

	public function hasDir($path)
	{
		return is_dir($this->getRealPath($path));
	}

	public function hasFile($path)
	{
		return file_exists($this->getRealPath($path));
	}

	public function delete($path ='')
	{
		$delete_path = $this->getRealPath($path);
		return unlink($delete_path);
	}

	public function createDir($path)
	{
		return mkdir($this->getRealPath($path));
	}

	public function create($path, $content)
	{
		$file_path = $this->getRealPath($path);
		if($this->hasFile($path)){
			throw new AppException("File [$file_path] is existed!");
		}else{
			$dir_path = rtrim($path, '/'.basename($path));
			if(!$this->hasDir($dir_path)){
				$this->createDir($dir_path);
			}
			return file_put_contents($file_path, $content) ? new FileSaved(ltrim($file_path, root_path)) : false;
		}
	}
}


 ?>
