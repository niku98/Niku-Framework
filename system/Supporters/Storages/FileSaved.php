<?php
namespace System\Supporters\Storages;
use \AppException;
/**
 * File class - Control file input
 */
class FileSaved
{
	protected $path;

	function __construct($path)
	{
		$this->path = $path;
		return $this;
	}

	public static function find($path)
	{
		return new static($path);
	}

	public function copyTo(string $path, string $name = '')
	{
		return Storage::folder($path)->create($name, $this->getContent());
	}

	public function delete()
	{
		return Storage::folder('')->delete($this->path);
	}

	public function getContent()
	{
		return file_get_contents(root_path.'/'.trim($this->path, '/'));
	}

	public function getExtension(){
		if($this->type == 'saved'){
			$extenstion = pathinfo( $this->path, PATHINFO_EXTENSION );
		}else{
			$extenstion = pathinfo( $this->path['name'], PATHINFO_EXTENSION );
		}

		return $extenstion;
	}

	public function getName(){
		return basename($this->path, '.'.$this->getExtension());;
	}

	public function getFullName(){
		return basename($this->path);
	}

	public function getMimeType(){
		$allType = require root_path.'resources/base/mime_type.php';

		return $allType[strtolower($this->getExtension())] ?? NULL;
	}

	public function isValid(){
		return Storage::folder('')->has($this->path);
	}

	public function getPath(){
		return root_path.ltrim($this->path, '/');
	}
}


 ?>
