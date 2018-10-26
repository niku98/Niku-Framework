<?php
namespace System\Supporters;
use ArrayAccess;
use Countable;
use Iterator;

class Collection implements ArrayAccess, Countable, Iterator
{
	/**
	 * Content Collection items
	 *
	 * @var array
	 */
	protected $items = [];


	/*-------------------------------
	Magic methods
	-------------------------------*/

	/**
	 * Constructor
	 *
	 * @param     array
	 * @return    System\Supporters\Collection
	 */
	public function __construct(array $items)
	{
		$this->items = $items;

		return $this;
	}

	/**
	 * Change Debug info
	 *
	 * @param     null
	 * @return    array
	 */
	public function __debugInfo()
	{
		return $this->items;
	}

	/**
	 * Countable functio
	 *
	 * @param     null
	 * @return    int
	 */
	public function count()
	{
		return $this->size();
	}

	/**
	 * Method to heck if offset item exists
	 *
	 * @param     mixed
	 * @return    bool
	 */
	public function offsetExists($offset)
	{
		return $this->hasKey($offset);
	}

	/**
	 * Method to get offset item via [] operator
	 *
	 * @param     mixed
	 * @return    mixed
	 */
	public function offsetGet($offset)
	{
		return $this->items[$offset];
	}

	/**
	 * Method to set offset item via [] operator
	 *
	 * @param     mixed
	 * @return    void
	 */
	public function offsetSet($offset, $value)
	{
		$this->items[$offset] = $value;
	}

	/**
	 * Method to unsset offset item via [] operator
	 *
	 * @param     mixed
	 * @return    void
	 */
	public function offsetUnset($offset)
	{
		unset($this->items[$offset]);
	}

	public function rewind()
    {
        reset($this->items);
    }

    public function current()
    {
        return current($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function valid()
    {
        $key = key($this->items);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }

	/*-----------------------------
	Base Methods
	-----------------------------*/

	/**
	 * Method to make a walk recursive for items
	 *
	 * @param     callable
	 * @param     mixed
	 * @return    bool
	 */
	public function recursive($callback, $user_data = NULL){
		if(!is_callable($callback)){
			throw new appException("Parameter for Collection::recursive() mus be a callable!");
		}
		array_walk_recursive($this->items, $callback, $user_data);

		return $this;
	}

	/**
	 * Method to check if items has key
	 *
	 * @param     mixed
	 * @return    bool
	 */
	public function hasKey($offset)
	{
		return isset($this->items[$offset]);
	}

	/**
	 * Check if item exists
	 *
	 * @param     mixed
	 * @return    bool
	 */
	public function has($value)
	{
		return in_array($this->items, $value);
	}

	/**
	 * Get number of items
	 *
	 * @param     null
	 * @return    int
	 */
	public function size(){
		return count($this->items);
	}

	/**
	 * Get all value without key
	 *
	 * @param     null
	 * @return    array
	 */
	public function values(){
		return array_values($this->items);
	}

	/**
	 * Get all key without value
	 *
	 * @param     null
	 * @return   array
	 */
	public function keys()
	{
		return array_keys($this->items);
	}

	/**
	 * Sort items
	 *
	 * @param     null|Callable
	 * @return   Collection
	 */
	public function sort(){
		$args = func_get_args();
		if(count($args) == 0){
			sort($this->items);
			return $this;
		}

		if(is_callable($args[0])){
			return $this->uSort($args[0]);
		}
	}

	/**
	 * Sort item by callback
	 *
	 * @param    Callable
	 * @return   Collection
	 */
	public function uSort($callback)
	{
		if(!is_callable($callback)){
			throw new appException("Parameter for Collection::uSort() mus be a callable!");
		}
		usort($this->items, $callback());
		return $this;
	}

	/**
	 * Change all item to array
	 *
	 * @param    null
	 * @return   array
	 */
	public function toArray()
	{
		$this->recursive(function(&$item, &$key){
			$item = (array)$item;
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
			$item = is_object($item) ? json_encode((array)$item) : json_encode($item);
		});

		return json_encode($this->items);
	}
}



 ?>
