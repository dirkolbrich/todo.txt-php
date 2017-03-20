<?php

namespace TodoTxt;

use TodoTxt\Task;

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
    protected $tasks = [];

    /**
     * list of uncompleted tasks
     *
     * @var array
     */
    protected $todo = [];

    /**
     * list of completed tasks
     *
     * @var array
     */
    protected $done = [];

    /**
     * Array of Projects in tasks
     *
     * @var array
     */
    protected $projects = [];

    /**
     * Array of Contexts in $tasks
     *
     * @var array
     */
    protected $contexts = [];

    /**
     * Array of Metadata in $tasks
     *
     * @var array
     */
    protected $metadata = [];

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
                    $this->addTask($input);
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
    protected function split(string $string)
    {
        $lines = [];

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
    public function addTask($task)
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
            $this->addTask($task);
        }
    }

    /**
     * add new task and mark as done immediately
     *
     * @param mixed $task
     */
    public function addDone($task)
    {
        $task = $this->addTask($task);
        $this->doTask($task->getId());
    }

    /**
     * add new task and set priority
     *
     * @param mixed $task
     * @param string @priority
     */
    public function addPriority($task, string $priority)
    {
        $task = $this->addTask($task);
        $task->setPrio($priority);
    }

    /**
     * add project from the task to the $projects list
     *
     * @param Task $task
     */
    protected function addProject(Task $task)
    {
        if (isset($task->projects)) {
            foreach ($task->projects as $project) {
                $this->projects[] = $project;
            }
        }
    }

    /**
     * add project from the task to the $projects list
     *
     * @param Task $task
     */
    protected function addContext(Task $task)
    {
        if (isset($task->contexts)) {
            foreach ($task->contexts as $context) {
                $this->contexts[] = $context;
            }
        }
    }

    /**
     * add metadata from the task to the $metadatas list
     +
     * @param Task $task
     */
    protected function addMetadata(Task $task)
    {
        if (isset($task->metadata)) {
            foreach ($task->metadata as $meta) {
                $this->metadata[] = $meta;
            }
        }
    }

    /**
     * get task with id
     *
     * @param string $id
     * @return Task|null
     */
    public function getTask(string $id)
    {
        foreach ($this->tasks as $task) {
            if ($task->getId() === $id) {
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
     * do a task, move from $todos to $done
     *
     * @param string $id
     */
    public function doTask(string $id)
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
            $this->doTask($task->gitId());
        }
    }

    /**
     * undo a task
     *
     * @param string $id
     */
    public function undoTask(string $id)
    {
        // check if task exists
        $task = $this->getTask($id);
        $task->uncomplete();
        $this->removeTaskFromDone($task->id);
        $this->todo[] = $task;
    }

    /**
     * undo all $done tasks
     */
    public function undoAll()
    {
        foreach ($this->done as $task) {
            $this->undoTask($task->getId());
        }
    }

    /**
     * edit a task
     *
     * @param string $id
     * @return Task|null
     */
    public function editTask(string $id)
    {
        // edit the text of the task
        // return new task
    }

    /**
     * delete a task from this todolist
     *
     * @param string $id
     */
    public function deleteTask(string $id)
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
    public function clearAll()
    {

    }

    /**
     * remove a task from the $todo list
     *
     * @param string $id
     */
    protected function removeTaskFromTodo(string $id)
    {
        foreach ($this->todo as $key => $todo) {
            if ($todo->getId() === $id) {
                unset($this->todo[$key]);
            }
        }
        $this->todo = array_values($this->todo);
    }

    /**
     * remove a task from the $done list
     *
     * @param string $id
     */
    protected function removeTaskFromDone(string $id)
    {
        foreach ($this->done as $key => $done) {
            if ($done->getId() === $id) {
                unset($this->done[$key]);
            }
        }
        $this->done = array_values($this->done);
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
    public function sortByPriority(string $priority = null)
    {
        // sort by $priority, case in-sensitive
    }

    /**
     * @param string $project
     */
    public function sortByProject(string $project = null)
    {
        // sort by project, standard alphabetical or via specific project
    }

    /**
     * @param string $context
     */
    public function sortByContext(string $context = null)
    {
        // sort by context, standard alphabetical or via specific context
    }

    /**
     * @param string $metadata
     */
    public function sortByMetaData(string $metadata = null)
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
                'tasks' => $this->tasks,
                'todo' => $this->todo,
                'done' => $this->done,
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
