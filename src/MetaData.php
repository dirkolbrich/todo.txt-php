<?php

namespace TodoTxt;

use TodoTxt\Exceptions\EmptyArrayException;
use TodoTxt\Exceptions\InvalidArrayException;

/**
 * Encapsulates a single project of a todo.txt list.
 */
class MetaData
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $value;

    /**
     * Create a new metadata from the found pattern in a raw task line.
     * @param array $metadata
     * @throws EmptyArrayException When $metadata is an empty array
     */
    public function __construct(array $metadata)
    {
        $metadata = array_filter($metadata);
        if (empty($metadata)) {
            throw new EmptyArrayException;
        }
        if (count($metadata) !== 3) {
            throw new InvalidArrayException;
        }
        $this->key = $metadata[1];
        $this->value = $metadata[2];        
    }
}
