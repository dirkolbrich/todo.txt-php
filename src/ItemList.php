<?php
declare(strict_types=1);

namespace TodoTxt;

/**
 * Encapsulates an array of items
 */
class ItemList implements \Countable, \ArrayAccess
{
    /**
     * count of the content of the list
     *
     * @var int
     */
    private $count = 0;

    /**
     * @var array
     */
    public $list = [];

    /**
     * add an item to the $list
     *
     * @param mixed $item
     */
    public function add($item)
    {
        $this->list[] = $item;
        ++$this->count;
    }

    /**
     * delete an item from the $list
     *
     * @param int $position
     * @return bool
     */
    public function delete(int $position): bool
    {
        if ($this->offsetExists($position)) {
            unset($this->list[$position]);
            $this->list = array_values($this->list);
            --$this->count;
            return true;
        }
        return false;
    }

    /**
     * return the first item of the $list
     *
     * @return mixed
     **/
    public function first()
    {
        return $this->list[0];
    }

    /**
     * return the first item of the $list
     *
     * @return mixed
     **/
    public function last()
    {
        return $this->list[$this->count() - 1];
    }

    /**
     * find the position of an item within list
     * @param mixed $arg
     * @return int | null
    */
    public function findPositionById(string $id)
    {
        foreach ($this->list as $key => $item) {
            if ($item->getId() == $id) {
                return $key;
            }
        }

        return null;
    }

    // implement \Countable Interface
    /**
     * count the content of $list
     *
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    // implement \ArrayAccess Interface
    /**
     * checks if a task at the specified line number exists
     *
     * @param integer $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->list[$offset]);
    }

    /**
     * @param integer $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->list[$offset]) ? $this->list[$offset] : null;
    }

    /**
     * @param integer $offset
     * @param string $value
     */
    public function offsetSet($offset, $value)
    {
        $this->list[$offset] = $value;
    }

    /**
     * @param integer $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->list[$offset]);
    }
}
