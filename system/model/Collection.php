<?php
namespace System\Model;
use System\Supporters\Collection as BaseCollection;

class Collection extends BaseCollection
{
	/**
	 * Change all item to array
	 *
	 * @param    null
	 * @return   array
	 */
	public function toArray()
	{
		$this->recursive(function(&$item, &$key){
			$item = $item->toArray();
		});

		return $this->items;
	}

	/**
	 * Change items to json
	 *
	 * @param    null
	 * @return   string
	 */
	public function toJson()
	{
		$this->recursive(function(&$item, &$key){
			$item = $item->toJson();
		});

		return json_encode($this->items);
	}
}


 ?>
