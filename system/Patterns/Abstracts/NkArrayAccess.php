<?php
namespace System\Patterns\Abstracts;
use ArrayAccess;
/**
 *
 */
abstract class NkArrayAccess implements ArrayAccess
{

	public function offsetSet($offset, $value) {
        $this->data[$offset] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}


 ?>
