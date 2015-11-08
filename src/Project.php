<?php

namespace TodoTxt;

use TodoTxt\Exceptions\EmptyStringException;

/**
 * Encapsulates a single project of a todo.txt list.
 */
class Project
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Create a new project from a raw line held in a todo.txt file.
     * @param string $project A raw task line
     * @throws EmptyString When $project is an empty string (or whitespace)
     */
    public function __construct($project)
    {
        $project = trim($project);
        if (strlen($project) == 0) {
            throw new EmptyStringException;
        }
        $this->name = $project;
    }

    /**
     * @return string $name
     */
    public function get()
    {
        return $this->name;
    }
}
