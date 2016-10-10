<?php

namespace TodoTxt;

/**
 * Encapsulates a complete todo.txt list.
 * Handles the adding and editing of tasks.
 *
 * @TODO: Make a ContextList and ProjectList class to hold contexts and
 *        projects (so we can do count($list->projects) etc.).
 */

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
     * Array of Projects in tasks
     *
     * @var array
     */
    protected $projects = array();

    /**
     * Array of Contexts in $tasks
     *
     * @var array
     */
    protected $contexts = array();

    /**
     * Array of Metadata in $tasks
     *
     * @var array
     */
    protected $metadata = array();

    /**
     * @param mixed $input
     */
    public function __construct($input = null)
    {
        // check for input type
        if (!is_null($input)) {
            switch ($input) {
                // $input is new line separated string
                case (is_string($input) && strstr($input, PHP_EOL)):
                    $this->splitString($input);
                    break;
                // $input is an array
                case (is_array($input)):
                    foreach ($input as $line) {
                        $this->addTask($line);
                    }
                    break;
                // $input is a simple string
                default:
                    $this->addTask($input);
                    break;
            }
        }
    }
   
    /**
     * @param mixed $string
     */
    public static function make($input = null)
    {
        // static function
    }
    
    /**
     * Split from a newline separated string into single lines 
     *
     * @param string $string A newline-separated string of tasks.
     * @return array $lines
     */
    public function splitString($string)
    {
        $lines = array();

        foreach (explode(self::$lineSeparator, $string) as $line) {
            $line = trim($line);
            if (strlen($line) > 0) {
                $lines[] = $line;
            }
        }

        return $lines;
    }

    /**
     * add a task to the $tasks array
     *
     * @param mixed $task
     */
    public function addTask($string)
    {
        if (!($string instanceof Task)) {
            $task = new Task((string) $string);
        }
        $this->tasks[] = $task;

        if ($task->isComplete()) {
            $this->done[] = $task;
        } else {
            $this->todo[] = $task;
        }

        $this->addProject($task);
        $this->addContext($task);
    }

    /**
     * add a task for multiple lines
     *
     * @param array $lines
     */
    public function addTasks(array $lines)
    {
        foreach ($lines as $string) {
            $this->addTask($string);
        }
    }

    /**
     * Parses single lines from a newline separated string
     *
     * @param string $string A newline-separated list of tasks.
     * @return array $lines
     */
    public function parseString($string)
    {
        $lines = array();
        
        foreach (explode(self::$lineSeparator, $string) as $line) {
            $line = trim($line);
            if (strlen($line) > 0) {
                $lines[] = $line;
            }
        }
        
        return $lines;
    }

    public function edit($position = null)
    {
        // edit the text of the task
    }

    /**
     * @param integer $position
     */
    public function delete($position = null)
    {
        // validate existinence of task
        // delete Task
        // remove projects and contexts if not used by other tasks
        // remove from $tasks, $todo or $done list
    }

    /**
     * clear $todos and archive done tasks to $done
     */
    public function archive()
    {
        // move completed tasks to $done
        // remove completed tasks from $tasks
    }

    /**
     * delete all tasks, todo, done, reset todoList to clear state
     */
    public function clear()
    {

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
     * sort todolist with standard sorting method
     */
    public function sort()
    {
        // sort standard
    }

    public function sortBy($arg)
    {
        // sort by argument
    }

    /**
     * @param string $priority
     */
    public function sortByPriority($priority = null)
    {
        // sort by $priority, case in-sensitive
    }

    /**
     * @param string $project
     */
    public function sortByProject($project = null)
    {
        // sort by project, standard alphabetical or via specific project
    }

    /**
     * @param string $context
     */
    public function sortByContext($context = null)
    {
        // sort by context, standard alphabetical or via specific context
    }

    /**
     * @param string $metadata
     */
    public function sortByMetaData($metadata = null)
    {
        // sort by metadata, standard alphabetical or via specific metadata
    }

    public function filterBy($arg)
    {
        // filter by argument
    }

    /**
     * get only open tasks
     *
     *@return array $todo
     */
    public function getTodo()
    {
        return $this->todo;
    }
    
    /**
     * get done tasks, helpful to write these to a done.txt file
     *
     * @return array $done
     */
    public function getDone()
    {
        return $this->done;
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

    // implement \ArrayAccess Interface
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

    // implement \Countable Interface
    /**
     * count all tasks
     *
     * @return integer
     */
    public function count()
    {
        return count($this->tasks);
    }

    // implement \SeekableIterator Interface
    /**
     * set $position to specific task
     *
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
     * forward $position by 1
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
    
    // implement \Serializable Interface
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
}
