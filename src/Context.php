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
        $context->name = $string;

        return $context;
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
        $this->name = $string;
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
