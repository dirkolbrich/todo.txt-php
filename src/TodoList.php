<?php

namespace TodoTxt;

use Task;

/**
 * Encapsulates a complete todo.txt list.
 * Handles the adding and editing of tasks.
 */

class TodoList implements \ArrayAccess, \Countable, \Serializable
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
                case (is_string($input) && strpos($input, PHP_EOL)):
                    $tasks = $this->split($input);
                    $this->addMultiple($tasks);
                    break;
                // $input is an array
                case (is_array($input)):
                    $this->addMultiple($input);
                    break;
                // $input is a simple string
                default:
                    $this->add($input);
                    break;
            }
        }
    }
   
    /**
     * named constructor
     *
     * @param mixed $input
     * @return TodoList
     */
    public static function make($input = null)
    {
        return new static($input);
    }
    
    /**
     * Split a newline separated string into single lines.
     *
     * @param string $string A newline-separated string of tasks.
     * @return array $lines
     */
    protected function split($string)
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
     * add a new task
     *
     * @param mixed $task
     * @return Task
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
        $this->addMetadata($task);
        
        return $task;
    }

    /**
     * add multiple new tasks
     *
     * @param array $tasks
     */
    public function addMultiple(array $tasks)
    {
        foreach ($tasks as $task) {
            $this->add($task);
        }
    }
    
    /**
     * add new task and mark as done immediately
     *
     * @param string $task
     */
    public function addDone($task)
    {
        $task = $this->add($task);
        $task->complete();
    }

    /**
     * add new task and set priority
     *
     * @param string $task
     * @param string @priority
     */
    public function addPriority($task, $priority)
    {
        $task = $this->add($task);
        $task->setPrio($priority);
    }

    /**
     * add project from the task to the $projects list
     *
     * @param Task $task
     */
    protected function addProject($task)
    {
        if (isset($task->projects)) {
            foreach ($task->projects as $project) {
                $this->projects[] = $project;
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
                $this->contexts[] = $context;
            }
        }
    }
    
    /**
     * @param Task $task
     */
    protected function addMetadata($task)
    {
        if (isset($task->metadata)) {
            foreach ($task->metadata as $meta) {
                $this->metadata[] = $meta;
            }
        }
    }


    /**
     * do a task, move from $todos to $done
     *
     * @param string $id
     */
    public function do($id)
    {
        // check if task exists
        $task = $this->getTask($id);
        $task->complete();
        $this->removeTaskFromTodo($task);
        $this->done[] = $task;
    }
    
    /**
     * do all tasks
     */
    public function doAll()
    {
        foreach ($this->todo as $task) {
            $this->do($task->id);
        }
    }
    
    /**
     * undo a task
     *
     * @param string $id
     */
    public function undo($id)
    {
        // check if task exists
        $task = $this->getTask($id);
        $task->uncomplete();
        $this->removeTaskFromDone($task);
        $this->todo[] = $task;
    }

    /**
     * undo all $done tasks
     */
    public function undoAll()
    {
        foreach ($this->done as $task) {
            $this->undo($task->id);
        }
    }
    
    /**
     * edit a task
     *
     */
    public function edit($id, $task = null)
    {
        // edit the text of the task
    }

    /**
     * @param string $position
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
        // remove completed tasks from $todo
    }

    /**
     * delete all tasks, todo, done, reset todoList to clear state
     */
    public function clear()
    {

    }

    /**
     * remove a task from the $todo list
     *
     * @param Task
     */
    protected function removeTaskFromTodo($task)
    {
        foreach ($this->todo as $key => $todo) {
            if ($todo->id == $task->id) {
                unset($this->todo[$key]);
            }
        }
        $this->todo = array_values($this->todo);
    }

    /**
     * remove a task from the $done list
     *
     * @param Task
     */
    protected function removeTaskFromDone($task)
    {
        foreach ($this->done as $key => $done) {
            if ($done->id == $task->id) {
                unset($this->done[$key]);
            }
        }
        $this->done = array_values($this->done);
    }
    
    /**
     * get task with id
     *
     * @param integer $id
     * @return Task|null
     */
    public function getTask($id)
    {
        foreach ($this->tasks as $task) {
            if ($task->id === $id) {
                return $task;
            }
        }
        return null;
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
     * get only open tasks
     *
     * @return array $todo
     */
    public function getTodo()
    {
        return $this->todo;
    }
    
    /**
     * get only done tasks
     *
     * @return array $done
     */
    public function getDone()
    {
        return $this->done;
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
     * @return string
     */
    public function __toString()
    {
        $string = '';
        foreach ($this->tasks as $task) {
            $string .= $task . self::$lineSeparator;
        }
        
        return trim($string);
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
    
    // implement \Serializable Interface
    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(
            array(
                'tasks' => this->tasks,
                'todo' => this->todo,
                'done' => this->done,
            )
        );
    }
    
    /**
     * unserialize data into properties
     *
     * @param array $data
     * @return void
     */
    public function unserialize($data)
    {
        $data = unserialize($data);
        
        $this->tasks = $data['tasks'];
        $this->todo = $data['todo'];
        $this->done = $data['done'];
    }
}
