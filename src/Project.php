<?php
declare(strict_types=1);

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
    protected $name = '';

    /**
     * Create a new project from a raw string
     *
     * @param string $project
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
     * @param string $string - a string representing the name of a project
     * @return self
     */
    public static function withString(string $string): self
    {
        $project = new Project();

        $string = $project->validateString($string);
        $project->name = $string;

        return $project;
    }

    /**
     * get $name of this project
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * set $name of this project, recreate $id
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
