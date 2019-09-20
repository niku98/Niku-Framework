<?php
namespace System\Supporters\Storages;
use System\Patterns\Abstracts\File;

/**
 *
 */
class FileUpload extends File
{
	private $data;

	function __construct($data)
	{
		$this->data = $data;
		return $this;
	}

	public function saveTo($path, $name)
	{
		return Storage::folder($path)->create($name, $this->getContent());
	}

	public function isValid(){
		return $this->data['error'] === 0 && $this->checkHeaderWithCurrentExtension();
	}

	public function getError(){
		return $this->data['error'];
	}

	public function getPath()
	{
		return $this->data['tmp_name'];
	}

	public function getName()
	{
		return basename($this->data['name'], '.'.$this->getExtension());
	}

	public function getFullName()
	{
		return $this->data['name'];
	}

	public function getExtension()
	{
		return pathinfo( $this->data['name'], PATHINFO_EXTENSION );
	}

	public function getContent()
	{
		return file_get_contents($this->data['tmp_name']);
	}

	public function getMimeType(){
		return $this->data['type'];
	}

	public function checkMimeType()
	{
		// code...
	}
}


 ?>
