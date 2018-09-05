<?php
namespace system\model\relations;
use system\database\Database;
use system\model\Model;

abstract class Relation
{

	/**
	 * Main Model in relation
	 *
	 * @var system\model\Model
	 */
	protected $mainModel;

	/**
	 * Child Model in relation
	 *
	 * @var system\model\Model
	 */
	protected $subModel;

	/**
	 * Local Key in relation
	 *
	 * @var string
	 */
	protected $localKey;

	/**
	 * Foreign Key in relation
	 *
	 * @var string
	 */
	protected $foreignKey;

	/**
	 * Middle Table in relation - Just use for many to many relation
	 *
	 * @var string
	 */
	protected $middleTable;

	/**
	 * Database to build query and connect to server
	 *
	 * @var system\database\Database
	 */
	protected $db;


	public function __construct(Model $mainModel, Model $subModel, string $localKey, string $foreignKey, string $middleTable = '')
	{
		$this->mainModel = $mainModel;
		$this->subModel = $subModel;
		$this->localKey = $localKey;
		$this->foreignKey = $foreignKey;
		$this->middleTable = $middleTable;
		$this->db = new Database();
	}

	public function where(){
		
	}
}


 ?>
