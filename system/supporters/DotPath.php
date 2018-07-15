<?php
namespace system\supporters;
use system\patterns\Singleton;
/**
 * DotPath class to process path
 */
class DotPath extends Singleton
{

	/**
	 * Raw path from user
	 *
	 * @var     string
	 */
	private $path;

	/**
	 * Base path to get file from path
	 *
	 * @var     string
	 */
	private $base;

	protected function __construct(string $path, string $base = ''){
		$this->path = $path;
		$this->base = $base;
		return $this;
	}


	/**
	 * Split '.' character from path
	 *
	 * @param     void
	 * @return    array
	 */
	private function splitPath()
	{
		return explode('.', $this->path);
	}


	public function joinPath(array $path)
	{
		return implode($path, '/');
	}


	/**
	 * Find file by full path
	 *
	 * @param     void
	 * @return    void
	 * @author
	 * @copyright
	 */
	public function findFile()
	{
		$path_parts = $this->splitPath();
		var_dump(file_exists($path_parts));
	}

	public function __toString()
	{
		return trim($this->base, '/').'/'.$this->joinPath($this->splitPath());
	}

	public function setPath(string $path)
	{
		$this->path = $path;

		return $this;
	}

	public function getPath()
	{
		return $this->path;
	}
}


 ?>
