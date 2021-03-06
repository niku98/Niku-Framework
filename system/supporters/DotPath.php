<?php
namespace System\Supporters;

/**
 * DotPath
 */
class DotPath
{

	private function __construct()
	{
		// code...
	}

	public static function findFile(string $base, string $dots, $extensions = ['php'])
	{
		$parts = explode('.', $dots);
		$path = '';
		$base = trim($base, '/');
		foreach ($parts as $part) {
			$path .= '/'.$part;
			foreach ($extensions as $extension) {
				if(file_exists($base.$path.'.'.$extension)){
					$dots = ltrim($dots, implode(explode('/', $path), '.'));
					return [
						'file' => $base.$path.'.'.$extension,
						'last' => $dots
					];
				}
			}
		}

		return false;
	}

	public static function findInArray(array $array, string $dots)
	{
		$parts = explode('.', $dots);
		$finded = null;
		foreach ($parts as $part) {
			if($finded == null){
				if(!isset($array[$part])){
					return null;
				}

				$finded = $array[$part];
			}else{
				if(!isset($finded[$part])){
					return null;
				}

				$finded = $finded[$part];
			}
		}

		return $finded;
	}

	public static function findFileArray(string $base, string $dots)
	{
		$base = static::findFile($base, $dots);
		$array = require $base['file'];
		return static::findInArray($array, $base['last']);
	}
}



 ?>
