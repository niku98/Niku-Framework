<?php
namespace System\Patterns\Abstracts;


abstract class File
{
	abstract public function getName();
	abstract public function getFullName();
	abstract public function getExtension();
	abstract public function getPath();
	abstract public function getContent();
	abstract public function getMimeType();
	abstract public function isValid();


	public function convertBase64(bool $web = true)
	{
		$base64 = $web ? 'data:' . $this->getMimeType() . ';base64,' : '';
		$base64 .= base64_encode($this->getContent());
		return $base64;
	}

	public function isImage(){
		if(!$this->isValid()){
			return false;
		}

		if($this->checkHeaderWithCurrentExtension() === false){
			return false;
		}

		if(strtolower($this->getExtension()) !== 'jpg' && strtolower($this->getExtension()) !== 'jpeg'
		&& strtolower($this->getExtension()) !== 'png' && strtolower($this->getExtension()) !== 'gif' ){
			return false;
		}

		if(strpos($this->getMimeType(), 'image') === false){
			return false;
		}

		if(!getimagesize($this->getPath())){
			return false;
		}

		return true;
	}

	public function isWord()
	{
		if(!$this->isValid()){
			return false;
		}

		if($this->checkHeaderWithCurrentExtension() === false){
			return false;
		}

		if(strtolower($this->getExtension()) !== 'docx'){
			return false;
		}


		if(strpos($this->getMimeType(), 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') === false){
			return false;
		}

		return true;
	}

	public function isSpreadSheet(){
		if(!$this->isValid()){
			return false;
		}

		if($this->checkHeaderWithCurrentExtension() === false){
			return false;
		}

		if(strpos($this->getMimeType(), 'spreadsheet') === false
		&& strpos($this->getMimeType(), 'csv') === false
		&& strpos($this->getMimeType(), 'excel') === false
		){
			return false;
		}

		if(strtolower($this->getExtension()) !== 'csv' && strtolower($this->getExtension()) !== 'xlsx'
		&& strtolower($this->getExtension()) !== 'ods'){

			return false;
		}

		return true;
	}

	protected function getMimeTypeWithExtension($extenstion)
	{
		$allType = require root_path.'resources/base/mime_type.php';

		return $allType[strtolower($extenstion)] ?? NULL;
	}

	protected function checkHeaderWithCurrentExtension()
	{
		$header = $this->getMimeType();
		$extenstion = $this->getExtension();
		$true_header = $this->getMimeTypeWithExtension($extenstion);

		if($header !== $true_header){
			return false;
		}

		return true;
	}
}


 ?>
