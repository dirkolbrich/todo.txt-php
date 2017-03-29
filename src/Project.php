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
     * The md5 hash of the raw utf-8 encoded string
     *
     * @var string
     */
    protected $id = '';

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
            $this->id = $this->createId($string);
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
        $project->id = $project->createId($string);
        $project->name = $string;

        return $project;
    }

    /**
     * get $id of this project
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
