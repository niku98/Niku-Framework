<?php
namespace System\Model\Relations;
use System\Model\Model;
use AppException;

abstract class Relation
{

	/**
	 * Main Model in relation
	 *
	 * @var System\Model\Model
	 */
	protected $mainModel;

	/**
	 * Sub Model in relation
	 *
	 * @var System\Model\Model
	 */
	protected $subModel;

	/**
	 * Local Key in relation
	 *
	 * @var string
	 */
	protected $mainKey;

	/**
	 * Foreign Key in relation
	 *
	 * @var string
	 */
	protected $subKey;

	/**
	 * Middle Table in relation - Just use for many to many relation
	 *
	 * @var string
	 */
	protected $middleTable;

	protected $belongsToNull = false;


	public function __construct(Model $mainModel, Model $subModel, string $mainKey, string $subKey, string $middleTable = '')
	{
		$this->mainModel = $mainModel;
		$this->subModel = $subModel;
		$this->mainKey = $mainKey;
		$this->subKey = $subKey;
		$this->middleTable = $middleTable;
		$this->processConditionInRelation();

		return $this;
	}

	public function __call($method, $params)
	{
		$this->subModel = $this->subModel->$method(...$params);
		if(is_a($this->subModel, 'System\Model\Builder', true) || is_a($this->subModel, 'System\Model\Model', true)){
			return $this;
		}

		return $this->subModel;
	}

	protected function getMainModelKeyVal()
	{
		return $this->mainModel->{$this->mainKey};
	}

	protected function getSubModelKeyVal()
	{
		return $this->subModel->{$this->subKey};
	}

	public function insert()
	{
		$data = $this->processInsertData(...func_get_args());
		return $this->subModel->insert($data);
	}

	public function delete()
	{
		if(!$this->belongsToNull)
			return $this->subModel->delete();
		return $this;
	}

	public function count()
	{
		return $this->subModel->count();
	}

	public function get()
	{
		return $this->getResult();
	}

	public function update()
	{
		return $this->subModel->update(...func_get_args());
	}

	abstract protected function processInsertData();
	abstract protected function getResult();
	abstract protected function processConditionInRelation();
}


 ?>
