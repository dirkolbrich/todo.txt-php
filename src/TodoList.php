<?php
declare(strict_types=1);

namespace TodoTxt;

use TodoTxt\ItemList;
use TodoTxt\Task;

/**
 * Encapsulates a complete todo.txt list.
 * Handles the adding and editing of tasks.
 */

class TodoList implements \Serializable
{
    /**
     * @var string
     */
    public static $lineSeparator = "\n";

    /**
     * container for an array of all tasks
     *
     * @var ItemList
     */
    protected $tasks;

    /**
     * container for an array of uncompleted tasks
     *
     * @var ItemList
     */
    protected $todo;

    /**
     * list of completed tasks
     *
     * @var ItemList
     */
    protected $done;

    /**
     * Array of Projects in tasks
     *
     * @var ItemList
     */
    protected $projects;

    /**
     * Array of Contexts in $tasks
     *
     * @var ItemList
     */
    protected $contexts;

    /**
     * Array of Metadata in $tasks
     *
     * @var ItemList
     */
    protected $metadata;

    /**
     * @param mixed $input
     */
    public function __construct($input = null)
    {
        $this->tasks = new ItemList();
        $this->todo = new ItemList();
        $this->done = new ItemList();
        $this->projects = new ItemList();
        $this->contexts = new ItemList();
        $this->metadata = new ItemList();

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
    public static function make($input = null): TodoList
    {
        return new static($input);
    }

    /**
     * Split a newline separated string into single lines.
     *
     * @param string $string A newline-separated string of tasks.
     * @return array $lines
     */
    protected function split(string $string): array
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
    public function addTask($task): Task
    {
        if (!($task instanceof Task)) {
            $task = new Task((string) $task);
        }
        $this->tasks->add($task);

        if ($task->isComplete()) {
            $this->done->add($task);
        } else {
            $this->todo->add($task);
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
        $task->setPriority($priority);
    }

    /**
     * add project from the task to the $projects list
     *
     * @param Task $task
     */
    protected function addProject(Task $task)
    {
        if (!empty($task->projects->list)) {
            foreach ($task->projects->list as $project) {
                $this->projects->add($project);
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
        if (!empty($task->contexts->list)) {
            foreach ($task->contexts->list as $context) {
                $this->contexts->add($context);
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
        if (!empty($task->metadata->list)) {
            foreach ($task->metadata->list as $meta) {
                $this->metadata->add($meta);
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
        foreach ($this->tasks->list as $task) {
            if ($task->getId() === $id) {
                return $task;
            }
        }

        return null;
    }

    /**
     * get all tasks
     *
     * @return ItemList $tasks
     */
    public function getTasks(): ItemList
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
        $this->removeTaskFromTodo($task->getId());
        $this->done->add($task);
    }

    /**
     * do all tasks
     */
    public function doAll()
    {
        foreach ($this->todo->list as $task) {
            $this->doTask($task->getId());
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
        $this->removeTaskFromDone($task->getId());
        $this->todo->add($task);
    }

    /**
     * undo all $done tasks
     */
    public function undoAll()
    {
        foreach ($this->done->list as $task) {
            $this->undoTask($task->getId());
        }
    }

    /**
     * delete a task from this todolist
     *
     * @param string $id
     * @return self
     */
    public function deleteTask(string $id): self
    {
        // validate existinence of task
        $task = $this->getTask($id);

        // remove projects and contexts if not used by other tasks
        $projects = $task->projects;
        $contexts = $task->projects;
        $metadata = $task->projects;

        // remove from $tasks, $todo or $done list
        $this->tasks->delete($this->tasks->findPositionById($id));
        ($task->isComplete()) ? $this->done->delete($this->done->findPositionById($id)) : $this->todo->delete($this->todo->findPositionById($id));

        // remove projects, contexts and metadata from list, if not in use anymore

        // return the todolist without the deleted task
        return $this;
    }

    /**
     * delete $done tasks, clear projects, contexts and metadate, return cleared §todolist
     *
     * @return self
     */
    public function archive(): self
    {
        // move completed tasks to $done

        $doneProjects = [];
        $doneContexts = [];
        $doneMetadata = [];

        // collect used projects, contxts and metadata from $done tasks
        foreach ($this->done->list as $done) {
            foreach ($done->projects->list as $project) {
                $doneProjects[] = $project;
            }
            foreach ($done->contexts->list as $context) {
                $doneProjects[] = $context;
            }
            foreach ($done->metadata->list as $metadata) {
                $doneProjects[] = $metadata;
            }
        }

        foreach ($this->done->list as $done) {
            $this->deleteTask($done->getId());
        }

        return $this;
    }

    /**
     * delete all tasks, todo, done, reset todoList to clear state
     */
    public function clearAll(): TodoList
    {
        return new Todolist();
    }

    /**
     * remove a task from the $todo list
     *
     * @param string $id
     */
    protected function removeTaskFromTodo(string $id)
    {
        foreach ($this->todo->list as $key => $todo) {
            if ($todo->getId() === $id) {
                $this->todo->delete($key);
            }
        }
    }

    /**
     * remove a task from the $done list
     *
     * @param string $id
     */
    protected function removeTaskFromDone(string $id)
    {
        foreach ($this->done->list as $key => $done) {
            if ($done->getId() === $id) {
                $this->done->delete($key);
            }
        }
        $this->done->list = array_values($this->done->list);
    }

    /**
     * get only open tasks
     *
     * @return ItemList $todo
     */
    public function getTodo(): ItemList
    {
        return $this->todo;
    }

    /**
     * get only done tasks
     *
     * @return ItemList $done
     */
    public function getDone(): ItemList
    {
        return $this->done;
    }

    /**
     * get only the projects
     *
     * @return ItemList $projects
     */
    public function getProjects(): ItemList
    {
        return $this->projects;
    }

    /**
     * get only the contexts
     *
     * @return ItemList $contexts
     */
    public function getContexts(): ItemList
    {
        return $this->contexts;
    }

    /**
     * get only the metadata
     *
     * @return ItemList $metadata
     */
    public function getMetadata(): ItemList
    {
        return $this->metadata;
    }

    /**
     * sort todolist with standard sorting method
     *
     * @return self
     */
    public function sort(): self
    {
        // sort standard
    }

    /**
     * sort todolist by argument
     *
     * @param string $arg
     * @return self
     */
    public function sortBy(string $arg): self
    {
        // sort by argument
    }

    /**
     * sort todolist by priority
     *
     * @param string $priority
     * @return self
     */
    public function sortByPriority(string $priority = null): self
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

    public function filterBy(string $arg)
    {
        // filter by argument
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $string = '';
        foreach ($this->tasks as $task) {
            $string .= $task . self::$lineSeparator;
        }

        return trim($string);
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
