<?php
declare(strict_types=1);

namespace TodoTxt;

use TodoTxt\Exceptions\EmptyStringException;

/**
 * Encapsulates a single context of a todo.txt list.
 */
class Context
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
    public $name = '';

    /**
     * Create a new context from a raw string
     *
     * @param string $context
     */
    public function __construct(string $string = null)
    {
        if (!is_null($string)) {
            $string = $this->validateString($string);
            $this->id = $this->createId($string);
            $this->name = $string;
        }
    }

    /**
     * static constructor function
     *
     * @param string $string - a string representing the name of a context
     * @return self
     */
    public static function withString(string $string): self
    {
        $context = new Context();

        $string = $context->validateString($string);
        $context->id = $context->createId($string);
        $context->name = $string;

        return $context;
    }

    /**
     * get $id of this context
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * get $name of this context
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * set $name of this context, recreate $id
     *
     * @param string $string
     */
    public function setName(string $string)
    {
        $string = $this->validateString($string);
        $this->id = $this->createId($string);
        $this->name = $string;
    }

    /**
     * create the $id of the project, a md5 hash based on the utf-8 encoded raw string
     *
     * @param string $string
     * @return string
     */
    protected function createId(string $string): string
    {
        return md5(utf8_encode($string));
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
