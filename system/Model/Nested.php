<?php

namespace System\Model;
use AppException;

/**
 *
 */
class Nested extends Model
{
	protected $parent_col = 'parent_id';
	protected $order_col = 'order';
	protected $nk_nested_children = null;
	protected $nk_nested_parent = null;
	protected $nk_nested_same_level = null;

	public function getChildren($order = true)
	{
		if($this->nk_nested_children == null){
			$this->nk_nested_children = $this->where($this->getParentCol(), $this->data[$this->primaryKey]);
			if($order){
				$this->nk_nested_children->orderBy($this->order_col, 'ASC');
			}
			$this->nk_nested_children = $this->nk_nested_children->get();
		}

		return $this->nk_nested_children;
	}

	public function hasChildren($order = true)
	{
		return $this->getChildren($order)->count() > 0;
	}

	public function getParent()
	{
		if($this->nk_nested_parent == null){
			$this->nk_nested_parent = $this->where($this->primaryKey, $this->data[$this->getParentCol()])->first();
		}

		return $this->nk_nested_parent;
	}

	public function sameLevel()
	{
		if($this->nk_nested_same_level != null){
			$this->nk_nested_same_level = $this->where($this->getParentCol(), $this->data[$this->getParentCol()])->where($this->primaryKey, '<>', $this->data[$this->getParentCol()])->get();
		}

		return $this->nk_nested_same_level;
	}

	public function getParentCol()
	{
		return $this->parent_col;
	}

	public static function nested($order = true, $children = null)
	{
		if($children == null){
			$base = new static();
			$children = $base->where($base->getParentCol(), 0);
			if($order){
				$children->orderBy($base->order_col, 'ASC');
			}
			$children = $children->get();
		}

		for ($i=0; $i < $children->count(); $i++) {
			if($children[$i]->hasChildren($order)){
				static::nested($order, $children[$i]->getChildren($order));
			}
		}

		return $children;
	}
}


 ?>
