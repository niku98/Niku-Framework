<?php
namespace system\supporters;
use \AppException;
/**
 * File class - Control file input
 */
class File
{
	protected $data;
	private $type;

	function __construct($data, $type = 'saved')
	{
		$this->data = $data;
		$this->type = $type;
		return $this;
	}

	public function saveTo(string $public_path, string $new_name = '')
	{
		$path = root_path.'public/'.$public_path.'/'.$new_name;

		if(!is_dir(root_path.'public/'.$public_path)){
			mkdir(root_path.'public/'.$public_path);
		}

		if($this->type === 'saved')
			return file_put_contents($path, $this->getContent()) !== false ? new File($path) : false;
		else{
			return move_uploaded_file($this->data['tmp_name'], $path) !== false ? new File($path) : false;
		}
	}

	public function getContent()
	{
		if($this->type == 'saved')
			return file_get_contents(root_path.'public/'.$this->data);
		else{
			return file_get_contents($this->data['tmp_name']);
		}
	}

	public function getExtension(){
		if($this->type == 'saved'){
			$extenstion = pathinfo( $this->data, PATHINFO_EXTENSION );
		}else{
			$extenstion = pathinfo( $this->data['name'], PATHINFO_EXTENSION );
		}

		return $extenstion;
	}

	public function getName(){
		if($this->type == 'saved'){
			$name = basename($this->data, '.'.$this->getExtension());
		}else{
			$name = basename($this->data['name'], '.'.$this->getExtension());
		}

		return $name;
	}

	public function getFullName(){
		if($this->type == 'saved'){
			$name = basename($this->data);
		}else{
			$name = basename($this->data['name']);
		}

		return $name;
	}

	public function getMimeType(){
		$allType = require root_path.'resources/base/mime_type.php';
		$type = $this->type === 'saved' ? $allType[strtolower($this->getExtension())] ?? NULL : $this->data['type'];
		return $type ?? NULL;
	}

	public function getMimeTypeWithExtension($extenstion)
	{
		$allType = require root_path.'resources/base/mime_type.php';

		return $allType[strtolower($extenstion)] ?? NULL;
	}

	public function convertBase64(bool $web = true)
	{
		$base64 = $web ? 'data:' . $this->getMimeType() . ';base64,' : '';
		$base64 .= base64_encode($this->getContent());
		return $base64;
	}

	public function isValid(){
		if($this->type == 'saved')
			return file_exists(root_path.'public/'.$this->data);
		else{
			return $this->data['error'] === 0;
		}
	}

	public function getError(){
		if($this->type == 'saved')
			return 0;
		else{
			return $this->data['error'];
		}
	}

	public function getPath(){
		if($this->type == 'saved')
			return root_path.'public/'.$this->data;
		else{
			return $this->data['tmp_name'];
		}
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

	public function checkHeaderWithCurrentExtension()
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
