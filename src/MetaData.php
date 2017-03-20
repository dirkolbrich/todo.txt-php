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
     * The md5 hash of the raw utf-8 encoded string
     *
     * @var string
     */
    protected $id;

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
     *
     * @param array $metadata
     * @throws EmptyArrayException - When $metadata is an empty array
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

        $this->id = $this->createId($metadata['full']);
        $this->key = $metadata['key'];
        $this->value = $metadata['value'];
    }

    /**
     * create the $id of the metadata, a md5 hash based on the utf-8 encoded raw string
     *
     * @param string $string
     * @return string
     */
    protected function createId(string $string) {
        return md5(utf8_encode($string));
    }

   /**
     * get $id of this metadata
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }
}
