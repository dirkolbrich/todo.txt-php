<?php

namespace TodoTxt;

use TodoTxt\Exceptions\EmptyStringException;

/**
 * Encapsulates a single project of a todo.txt list.
 */
class Project
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
    public $project;

    /**
     * Create a new project from a raw line held in a todo.txt file.
     *
     * @param string $project A raw task line
     * @throws EmptyStringException when $project is an empty string (or whitespace)
     */
    public function __construct(string $project)
    {
        $project = trim($project);
        if (strlen($project) == 0) {
            throw new EmptyStringException;
        }

        $this->id = $this->createId($project);
        $this->project = $project;
    }

    /**
     * create the $id of the project, a md5 hash based on the utf-8 encoded raw string
     *
     * @param string $string
     * @return string
     */
    protected function createId(string $string) {
        return md5(utf8_encode($string));
    }

    /**
     * get $id of this project
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }
}
