<?php

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
    protected $name;

    /**
     * Create a new project from a raw line held in a todo.txt file.
     * @param string $context A raw task line
     * @throws EmptyStringException When $project is an empty string (or whitespace)
     */
    public function __construct($context)
    {
        $context = trim($context);
        if (strlen($context) == 0) {
            throw new EmptyStringException;
        }
        $this->name = $context;
    }

    /**
     * @return string $name
     */
    public function get()
    {
        return $this->name;
    }
}
