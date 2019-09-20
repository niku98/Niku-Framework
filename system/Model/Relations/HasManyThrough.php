<?php
namespace System\Model\Relations;
use System\Model\Model;

/**
 * Has Many Through - Relation
 */
class HasManyThrough
{

	/**
	 * Main Model in relation
	 *
	 * @var System\Model\Model
	 */
	protected $mainModel;

	/**
	 * Middle Model in relation
	 *
	 * @var System\Model\Model
	 */
	protected $midModel;

	/**
	 * Last Model in relation
	 *
	 * @var System\Model\Model
	 */
	protected $lastModel;

	/**
	 * Main Primary Key in relation
	 *
	 * @var string
	 */
	protected $mainPriKey;

	/**
	 * Middle Primary Key in relation
	 *
	 * @var string
	 */
	protected $midPriKey;

	/**
	 * Middle Foreign Key in relation
	 *
	 * @var string
	 */
	protected $midForeignKey;

	/**
	 * Last Foreign Key in relation
	 *
	 * @var string
	 */
	protected $lastForeignKey;

	private $hasNull = true;


	public function __construct(Model $mainModel, Model $midModel, Model $lastModel, string $midForeignKey, string $lastForeignKey, string $mainPriKey, string $midPriKey)
	{
		$this->mainModel = $mainModel;
		$this->midModel = $midModel;
		$this->lastModel = $lastModel;
		$this->mainPriKey = $mainPriKey != '' ? $mainPriKey : $this->mainModel->getPrimaryKey();
		$this->midPriKey = $midPriKey != '' ? $midModel : $this->midModel->getPrimaryKey();
		$this->midForeignKey = $midForeignKey;
		$this->lastForeignKey = $lastForeignKey;

		$this->processConditionInRelation();

		return $this;
	}

	public function __call($method, $params)
	{
		$this->lastModel = $this->lastModel->$method(...$params);
		if(is_a($this->lastModel, 'System\Model\Builder', true) || is_a($this->lastModel, 'System\Model\Model', true)){
			return $this;
		}

		return $this->lastModel;
	}

	private function getMainModelPriKeyVal()
	{
		return $this->mainModel->{$this->mainPriKey};
	}

	private function getMidModelPriKeyVal()
	{
		return $this->midModel->{$this->midPriKey};
	}

	private function getListMiddleModelIds()
	{
		$this->midModel->where($this->midForeignKey, $this->getMainModelPriKeyVal())->get();
		$list_id = array();
		foreach ($listMidModel as $model) {
			$list_id = array_push($list_id, $model->{$this->midPriKey});
		}

		return $list_id;
	}

	protected function processConditionInRelation()
	{
		$list_middle_id = $this->getListMiddleModelIds();

		if(count($list_middle_id) !== 0){
			$this->hasNull = false;
			$this->lastModel->where($this->lastForeignKey, 'IN', $list_middle_id);
		}

		return $this;
	}

	public function first()
	{
		if($this->hasNull){
			return null;
		}

		return $this->lastModel->first();
	}

	public function get()
	{
		if($this->hasNull){
			return new \System\Supporters\Collection([]);
		}

		return $this->lastModel->get();
	}

	public function all()
	{
		return $this->get();
	}
}



 ?>
