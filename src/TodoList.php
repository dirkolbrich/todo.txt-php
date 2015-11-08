<?php

namespace TodoTxt;

class TodoList implements \ArrayAccess, \Countable, \SeekableIterator, \Serializable
{
    /**
     * @var string
     */
    public static $lineSeparator = "\n";
    
    /**
     * list of all tasks
     *
     * @var array
     */
    protected $tasks = array();

    /**
     * list of uncompleted tasks
     *
     * @var array
     */
    protected $todo = array();
    /**
     * list of completed tasks
     *
     * @var array
     */
    protected $done = array();
    
    /**
     * @var integer
     */
    protected $position = 0;

    /**
     * @var array
     */
    public $projectsList = array();

    /**
     * @var array
     */
    public $contextsList = array();

    /**
     * @param mixed $tasks
     */
    public function __construct($tasks = null)
    {
        $this->rewind();
        
        // check for input type
        if (!is_null($tasks)) {
            switch ($tasks) {
                case (is_array($tasks)):
                    $this->addTasks($tasks);
                    break;
                case (is_string($tasks) && strstr($tasks, PHP_EOL)):
                    $this->parseTasks($tasks);
                    break;
                default:
                    $this->addTask($tasks);
                    break;
            }
        }
    }
    
    /**
     * add a task to the $tasks array
     *
     * @param mixed $task
     */
    public function addTask($task)
    {
        if (!($task instanceof Task)) {
            $task = new Task((string) $task);
        }
        $this->tasks[] = $task;
    }
    
    /**
     * @param array $tasks
     */
    public function addTasks(array $tasks)
    {
        foreach ($tasks as $task) {
            $this->addTask($task);
        }
    }
    
    /**
     * Parses tasks from a newline separated string
     *
     * @param string $taskFile A newline-separated list of tasks.
     */
    public function parseTasks($taskFile)
    {
        foreach (explode(self::$lineSeparator, $taskFile) as $line) {
            $line = trim($line);
            if (strlen($line) > 0) {
                $this->addTask($line);
            }
        }
    }
    
    /**
     * get task at position
     *
     * @param integer $position
     * @return Task
     */
    public function getTask($position)
    {
        $this->seek($position);
        return $this->tasks[$position];
    }
    /**
     * get all tasks
     *
     * @return array $tasks
     */
    public function getTasks()
    {
        return $this->tasks;
    }
    
    /**
     * @param Task $task
     */
    protected function addProject($task)
    {
        if (isset($task->projects)) {
            foreach ($task->projects as $project) {
                $this->projectList[] = $project;
            }
        }
    }

    /**
     * @param Task $task
     */
    protected function addContext($task)
    {
        if (isset($task->contexts)) {
            foreach ($task->contexts as $context) {
                $this->contextList[] = $context;
            }
        }
    }
    /**
     * @return string
     */
    public function __toString()
    {
        $file = '';
        foreach ($this->tasks as $task) {
            $file .= $task . self::$lineSeparator;
        }
        
        return trim($file);
    }
    
    /**
     * checks if a task at the specified line number exists
     *
     * @param integer $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->tasks[$offset]);
    }
    
    /**
     * @param integer $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->tasks[$offset]) ? $this->tasks[$offset] : null;
    }
    
    /**
     * @param integer $offset
     * @param string $value
     */
    public function offsetSet($offset, $value)
    {
        $this->tasks[$offset] = $value;
    }
    
    /**
     * @param integer $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->tasks[$offset]);
    }
    
    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->tasks);
    }
    
    /**
     * unserialize the $tasks array
     *
     * @param array $tasks
     * @return void
     */
    public function unserialize($tasks)
    {
        $this->tasks = unserialize($tasks);
    }
    
    /**
     * @param integer $position
     */
    public function seek($position)
    {
        $this->position = $position;
        if (!$this->valid()) {
            throw new \OutOfBoundsException("Cannot seek to position $position.");
        }
    }
    
    /**
     * get task at current position
     *
     * @return Task $task
     */
    public function current()
    {
        return $this->tasks[$this->position];
    }
    
    /**
     * get current position
     *
     * @return integer
     */
    public function key()
    {
        return $this->position;
    }
    
    /**
     * forwar $position by 1
     *
     * @return void
     */
    public function next()
    {
        ++$this->position;
    }
    
    /**
     * resets $position
     *
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }
    
    /**
     * validate is a Task is at current position
     *
     * @return bool
     */
    public function valid()
    {
        return isset($this->tasks[$this->position]);
    }
    
    /**
     * count all tasks
     *
     * @return integer
     */
    public function count()
    {
        return count($this->tasks);
    }
}
