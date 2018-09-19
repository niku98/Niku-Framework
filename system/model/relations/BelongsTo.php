<?php
namespace system\model\relations;

/**
 * Belongs To - Relation
 */
class BelongsTo extends HasOne
{
	public function associate($object)
	{
		if(!is_a($object, get_class($this->subModel), true)){
			throw new AppException('Model need to associate must be an instance of '.get_class($this->subModel));
		}

		$this->mainModel->{$this->mainKey} = $object->{$this->subModel};
		$object->save();
		return $this;
	}

	public function dissociate()
	{
		$this->mainModel->{$this->mainKey} = null;
		$this->mainModel->save();
		return $this;
	}
}



 ?>
