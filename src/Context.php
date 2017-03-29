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
    protected $id;

    /**
     * @var string
     */
    public $context;

    /**
     * Create a new project from a raw line held in a todo.txt file.
     * @param string $context A raw task line
     * @throws EmptyStringException When $project is an empty string (or whitespace)
     */
    public function __construct(string $context)
    {
        $context = trim($context);
        if (strlen($context) == 0) {
            throw new EmptyStringException;
        }

        $this->id = $this->createId($context);
        $this->context = $context;
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
     * get $id of this context
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
