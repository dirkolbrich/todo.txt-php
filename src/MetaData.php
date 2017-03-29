<?php
declare(strict_types=1);

namespace TodoTxt;

use TodoTxt\Exceptions\EmptyArrayException;
use TodoTxt\Exceptions\EmptyStringException;
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
    protected $id = '';

    /**
     * @var string
     */
    protected $key = '';

    /**
     * @var string
     */
    protected $value = '';

    /**
     * Create a new metadata from the found pattern in a raw task line.
     *
     * @param array $array
     * @throws EmptyArrayException - When $metadata is an empty array
     */
    public function __construct(array $array = null)
    {
        if (!is_null($array)) {
            $array = $this->validateArray($array);

            $this->id = $this->createId($array['full']);
            $this->key = $array['key'];
            $this->value = $array['value'];
        }
    }

    /**
     * static constructor function
     *
     * @param array $array - an array with the contents representing the metadata
     * @return self
     */
    public static function withArray(array $array): self
    {
        $metadata = new MetaData();

        $array = $metadata->validateArray($array);
        $metadata->id = $metadata->createId($array['full']);
        $metadata->key = $array['key'];
        $metadata->value = $array['value'];

        return $metadata;
    }

   /**
     * get $id of this metadata
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

   /**
     * set $key of this metadata
     *
     * @param string $string
     * @return self
     */
    public function setKey(string $string): self
    {
        $string = $this->validateString($string);
        $this->key = $string;
        $this->id = $this->createId($string . ':' . $this->value);

        return $this;
    }

   /**
     * get $key of this metadata
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

   /**
     * set $value of this metadata
     *
     * @param string $string
     * @return self
     */
    public function setValue(string $string): self
    {
        $string = $this->validateString($string);
        $this->value = $string;
        $this->id = $this->createId($this->key . ':' . $string);

        return $this;
    }

   /**
     * get $value of this metadata
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * create the $id of the metadata, a md5 hash based on the utf-8 encoded raw string
     *
     * @param string $string
     * @return string
     */
    protected function createId(string $string): string
    {
        return md5(utf8_encode($string));
    }

    /**
     * validate the metadata array
     * array should be formed
     * array[
     *      'full' => full represntation of the metadata 'key:value',
     *      'key' => key of the metadata pair,
     *      'value' => value of the metadata pair,
     * ]
     *
     * @param array $array
     * @return array
     */
    protected function validateArray(array $array): array
    {
        $array = array_filter($array);

        // is array empty?
        if (empty($array)) {
            throw new EmptyArrayException;
        }

        // has array 3 items with the correct keys set?
        if ( (count($array) !== 3) && ( !array_key_exists('full', $array) || !array_key_exists('key', $array) || !array_key_exists('full', $array) )) {
            throw new InvalidArrayException;
        }

        return $array;
    }

    /**
     * validate the string
     *
     * @param string $string
     * @return string
     */
    protected function validateString(string $string): string
    {
        $string = trim($string);
        if (strlen($string) == 0) {
            throw new EmptyStringException;
        }

        return $string;
    }
}
