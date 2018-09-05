<?php
namespace system\supporters;

/**
 * DotPath
 */
class DotPath
{

	private function __construct()
	{
		// code...
	}

	public static function findFile($base, $dots)
	{
		$parts = explode('.', $dots);
		$path = '';
		$base = trim($base, '/');
		foreach ($parts as $part) {
			$path .= '/'.$part;
			if(file_exists($base.$path)){
				$dots = ltrim($dots, implode(explode('/', $path), '.'));
				return [
					'file' => $base.$path,
					'last' => $dots
				];
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
}



 ?>
