<?php
namespace system\model\relations;
use system\model\Model;
use AppException;

abstract class Relation
{

	/**
	 * Main Model in relation
	 *
	 * @var system\model\Model
	 */
	protected $mainModel;

	/**
	 * Sub Model in relation
	 *
	 * @var system\model\Model
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


	public function __construct(Model $mainModel, Model $subModel, string $mainKey, string $subKey, string $middleTable = '')
	{
		$this->mainModel = $mainModel;
		$this->subModel = $subModel;
		$this->mainKey = $mainKey;
		$this->subKey = $subKey;
		$this->middleTable = $middleTable;
		return $this;
	}

	public function __call($method, $params)
	{
		$this->subModel = $this->subModel->$method(...$params);
		if(!is_object($this->subModel) || !is_a($this->subModel, 'system\model\Model', true)){
			return $this->subModel;
		}
		return $this;
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
		$this->processConditionDelete();
		return $this->subModel->delete();
	}

	public function count()
	{
		$this->processConditionInRelation();
		return $this->subModel->count();
	}

	public function get()
	{
		$this->processConditionInRelation();
		return $this->getResult();
	}

	public function update()
	{
		$this->processConditionInRelation();
		return $this->subModel->update();
	}

	abstract protected function processInsertData();
	abstract protected function getResult();
	abstract protected function processConditionInRelation();
}


 ?>
