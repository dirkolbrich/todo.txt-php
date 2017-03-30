<?php
declare(strict_types=1);

namespace TodoTxt;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Encapsulates an array of items
 */
class ItemList implements Countable, ArrayAccess, IteratorAggregate
{
    /**
     * @var array
     */
    protected $list;

    /**
     * constructor with optional parameter
     *
     * @param array $array
     */
    public function __construct(array $array = null)
    {
        ($array == null) ? : $this->list = $array ;
    }

    /**
     * statik constructor with optional parameter
     *
     * @param array $array
     * @return self
     */
    public static function make(array $array = null): self
    {
        $list = new ItemList();
        $list->list = ($array == null) ? null : $array;

        return $list;
    }

    /**
     * get $list as array
     *
     * @return array|null
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Determine if the collection is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->list);
    }

    /**
     * add an item to the $list
     *
     * @param mixed $item
     * @return self
     */
    public function add($item): self
    {
        $this->list[] = $item;
        return $this;
    }

    /**
     * Push an item onto the beginning of the collection.
     *
     * @param  mixed  $item
     * @return self
     */
    public function prepend($item): self
    {
        $this->list = array_unshift($this->list, $item);
        return $this;
    }

    /**
     * Push an item onto the end of the collection.
     *
     * @param  mixed  $item
     * @return $this
     */
    public function push($item): self
    {
        return $this->add($item);
    }

    /**
     * delete an item from the $list
     *
     * @param  int  $position
     */
    public function delete(int $position)
    {
        if ($this->offsetExists($position)) {
            unset($this->list[$position]);
            $this->list = array_values($this->list);
            return true;
        }
        return false;
    }

    /**
     * Get and remove the first item from the collection.
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->list);
    }

    /**
     * Get and remove the last item from the collection.
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->list);
    }

    /**
     * implementing map
     *
     * @param   callable $callback
     * @return  static
     */
    public function map(callable $callback): self
    {
        return new static(array_map($callback, $this->list));
    }

    /**
     * implementing filter
     *
     * @param   callable $callback
     * @return  static
     */
    public function filter(callable $callback): self
    {
        return new static(array_values(array_filter($this->list, $callback)));
    }

    /**
     * implementing each
     *
     * @param   callable $callback
     * @return  void
     */
    public function each(callable $callback)
    {
        foreach ($this->list as $item) {
            $callback($item);
        }
    }

    /**
     * implementing reject
     *
     * @param callable $callback
     * @return self
     */
    public function reject(callable $callback): self
    {
        $result = [];
        foreach ($this->list as $item) {
            if (!$callback($item)) {
                $result[] = $item;
            }
        }

        $this->list = $result;
        return $this;
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
     * Reverse items order.
     *
     * @return static
     */
    public function reverse(): self
    {
        return new static(array_reverse($this->list, false));
    }

    /**
     * find the position of an item within list
     *
     * @param   string   $aid
     * @return  int|null
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
        return count($this->list);
    }

    // implement \IteratorAggregate Interface
    /**
     * iterate of $list
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->list);
    }

    // implement \ArrayAccess Interface
    /**
     * checks if a task at the specified line number exists
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->list[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->list[$offset]) ? $this->list[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->list[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->list[$offset]);
    }
}
