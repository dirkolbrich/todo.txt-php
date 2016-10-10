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
     * Array of Porjects in tasks
     *
     * @var array
     */
    protected $projectsList = array();

    /**
     * Array of Contexts in $tasks
     *
     * @var array
     */
    protected $contextsList = array();

    /**
     * Array of Metadata in $tasks
     *
     * @var array
     */
    protected $metadataList = array();

    /**
     * @param mixed $tasks
     */
    public function __construct($tasks = null)
    {
        $this->rewind();
        
        // check for input type
        if (!is_null($tasks)) {
            switch ($tasks) {
                case (is_string($tasks) && strstr($tasks, PHP_EOL)):
                    $this->parse($tasks);
                    break;
                case (is_array($tasks)):
                    foreach ($tasks as $task) {
                        $this->add($task);
                    }
                    break;
                default:
                    $this->add($tasks);
                    break;
            }
        }
    }
    
    /**
     * Parses tasks from a newline separated string
     *
     * @param string $taskFile A newline-separated list of tasks.
     */
    public function parse($taskFile)
    {
        foreach (explode(self::$lineSeparator, $taskFile) as $line) {
            $line = trim($line);
            if (strlen($line) > 0) {
                $this->add($line);
            }
        }
    }

    /**
     * add a task to the $tasks array
     *
     * @param mixed $task
     */
    public function add($task)
    {
        if (!($task instanceof Task)) {
            $task = new Task((string) $task);
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
     * @param integer $position
     */
    public function complete($position = null)
    {
        // validate existinence of task
        // validate isComplete
        // complete Task
        // move to $done list, remove from $todo list
    }

    /**
     * @param integer $position
     */
    public function uncomplete($position = null)
    {
        // validate existinence of task
        // validate isComplete
        // uncomplete Task
        // move to $todo list, remove from $done list
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

    }

    /**
     * @param string $metadata
     */
    public function sortByMetaData($metadata = null)
    {

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
