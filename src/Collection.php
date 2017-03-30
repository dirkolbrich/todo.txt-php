<?php
declare(strict_types=1);

namespace TodoTxt;

use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;

/**
 * Encapsulates an array of items
 */
class Collection implements Countable, ArrayAccess, IteratorAggregate
{
    /**
     * @var array
     */
    protected $items;

    /**
     * constructor with optional parameter
     *
     * @param array $array
     */
    public function __construct(array $array = null)
    {
        ($array == null) ? : $this->items = $array ;
    }

    /**
     * statik constructor with optional parameter
     *
     * @param array $array
     * @return self
     */
    public static function make(array $array = null): self
    {
        return new static($array);
    }

    /**
     * get $items as array
     *
     * @return array|null
     */
    public function get()
    {
        return $this->items;
    }

    /**
     * Determine if the collection is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * add an item to the $list
     *
     * @param mixed $item
     * @return self
     */
    public function add($item): self
    {
        $this->items[] = $item;
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
        $this->items = array_unshift($this->items, $item);
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
     * delete an item from collection
     *
     * @param  int  $position
     */
    public function delete(int $position)
    {
        if ($this->offsetExists($position)) {
            unset($this->items[$position]);
            $this->items = array_values($this->items);
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
        return array_shift($this->items);
    }

    /**
     * Get and remove the last item from the collection.
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * implementing map
     *
     * @param   callable $callback
     * @return  static
     */
    public function map(callable $callback): self
    {
        return new static(array_map($callback, $this->items));
    }

    /**
     * implementing filter
     *
     * @param   callable $callback
     * @return  static
     */
    public function filter(callable $callback): self
    {
        return new static(array_values(array_filter($this->items, $callback)));
    }

    /**
     * implementing each
     *
     * @param   callable $callback
     * @return  void
     */
    public function each(callable $callback)
    {
        foreach ($this->items as $item) {
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
        foreach ($this->items as $item) {
            if (!$callback($item)) {
                $result[] = $item;
            }
        }

        $this->items = $result;
        return $this;
    }

    /**
     * return the first item of the $list
     *
     * @return mixed
     **/
    public function first()
    {
        return $this->items[0];
    }

    /**
     * return the first item of the $list
     *
     * @return mixed
     **/
    public function last()
    {
        return $this->items[$this->count() - 1];
    }


    /**
     * Reverse items order.
     *
     * @return static
     */
    public function reverse(): self
    {
        return new static(array_reverse($this->items, false));
    }

    /**
     * find the position of an item within the collection
     *
     * @param   string   $aid
     * @return  int|null
    */
    public function findPositionById(string $id)
    {
        foreach ($this->items as $key => $item) {
            if ($item->getId() == $id) {
                return $key;
            }
        }

        return null;
    }

    // implement \Countable Interface
    /**
     * count the content of $items
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    // implement \IteratorAggregate Interface
    /**
     * iterate of $list
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
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
        return isset($this->items[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}
