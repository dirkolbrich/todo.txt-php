<?php
declare(strict_types=1);

namespace TodoTxt;

use TodoTxt\Task;
use TodoTxt\Collection;

/**
 * Encapsulates a complete todo.txt list.
 * Handles the adding and editing of tasks.
 */

class TodoList
{
    /**
     * @var string
     */
    public static $lineSeparator = "\n";

    /**
     * container for an array of all tasks
     *
     * @var Collection
     */
    protected $tasks;

    /**
     * container for an array of uncompleted tasks
     *
     * @var Collection
     */
    protected $todo;

    /**
     * container for completed tasks
     *
     * @var Collection
     */
    protected $done;

    /**
     * container for Projects in tasks
     *
     * @var Collection
     */
    protected $projects;

    /**
     * container for Contexts in $tasks
     *
     * @var Collection
     */
    protected $contexts;

    /**
     * container for Metadata in $tasks
     *
     * @var Collection
     */
    protected $metadata;

    /**
     * @param mixed $input
     */
    public function __construct($input = null, Collection $collection = null)
    {
        //injecting Collection dependency
        if (is_null($collection)) {
            $this->tasks = new Collection();
            $this->todo = new Collection();
            $this->done = new Collection();
            $this->projects = new Collection();
            $this->contexts = new Collection();
            $this->metadata = new Collection();
        } else {
            $this->tasks = $collection;
            $this->todo = $collection;
            $this->done = $collection;
            $this->projects = $collection;
            $this->contexts = $collection;
            $this->metadata = $collection;
        }

        if (!is_null($input)) {
            // validate input type
            $tasks = $this->validateInput($input);

            // process each task
            foreach ($tasks as $task) {
                $this->processTask($task);
            }
        }
    }

    /**
     * named constructor
     *
     * @param mixed $input
     * @return TodoList
     */
    public static function make($input = null, Collection $collection = null): TodoList
    {
        return new static($input, $collection);
    }

    /**
     * statik constructor with string
     *
     * @param string $string
     * @return self
     */
    public static function withString(string $string, Collection $collection = null): self
    {
        $list = new TodoList();

        if (strpos($string, PHP_EOL)) {
            $lines = $tlist->splitString($string);
            $list->addMultipleTasks($lines);
        } else {
            // simple string
            $list->addTask($string);
        }

        return $list;
    }

    /**
     * statik constructor with array
     *
     * @param array $array
     * @return self
     */
    public static function withArray(array $array, Collection $collection = null): self
    {
        $list = new TodoList();
        $list->addMultipleTasks($array);

        return $list;
    }

    /**
     * statik constructor with array
     *
     * @param Task $task
     * @return self
     */
    public static function withTask(Task $task, Collection $collection = null): self
    {
        $list = new TodoList();
        $list->addTask($task);

        return $list;
    }

    /**
     * validate Input for type
     *
     * @param mixed $input
     * @return array
     */
    protected function validateInput($input = null): array
    {
        $tasks = [];

        if (is_null($input)) {
            return $tasks;
        }

        switch ($input) {
            // $input is an array - enter recursive validation
            case (is_array($input)):
                foreach ($input as $item) {
                    $tasks = array_merge($tasks, $this->validateInput($item));
                }
                break;
            // $input is already a single Task class
            case ($input instanceof Task):
                $tasks[] = $input;
                break;
            // $input is new line separated string
            case (is_string($input) && strpos($input, PHP_EOL)):
                $lines = $this->splitString($input);
                $tasks = array_merge($tasks, $lines);
                break;
            // $input is simple string
            case (is_string($input)):
                $tasks[] = $input;
                break;
            // $input is something else, return empty
            default:
                break;
        }

        // make sure, every task is a Task object
        foreach ($tasks as $key => $task) {
            if (!$task instanceof Task) {
                $tasks[$key] = new Task($task);
            }
        }

        return $tasks;
    }

    /**
     * Split a newline separated string into single lines.
     *
     * @param string $string A newline-separated string of tasks.
     * @return array $lines
     */
    protected function splitString(string $string): array
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
     * process a single Task to integrate into $todolist
     *
     * @param Task $task
     * @return Task
     */
    protected function processTask(Task $task): Task
    {
        // add task to $tasks collection
        $this->tasks->add($task);

        // validate and sort to $todo od $done collection
        if ($task->isComplete()) {
            $this->done->add($task);
        } else {
            $this->todo->add($task);
        }

        // add project, context and metadata of task to collections
        $this->addProject($task);
        $this->addContext($task);
        $this->addMetadata($task);

        return $task;
    }

    /**
     * add a new task
     *
     * @param mixed $task
     * @return self
     */
    public function addTask($task): self
    {
        $validated = $this->validateInput($task);

        foreach ($validated as $task) {
            $this->processTask($task);
        }

        return $this;
    }

    /**
     * add multiple new tasks
     *
     * @param array $array
     */
    public function addMultipleTasks(array $array): self
    {
        foreach ($array as $item) {
            $this->addTask($item);
        }

        return $this;
    }

    /**
     * add new task and mark as done immediately
     *
     * @param mixed $task
     */
    public function addDone($task)
    {
        $tasks = $this->validateInput($task);
        $task = $tasks[0]->complete();
        $task = $this->processTask($task);

        return $this;
    }

    /**
     * add new task and set priority
     *
     * @param mixed $task
     * @param string @priority
     */
    public function addPriority($task, string $priority)
    {
        $task = $this->validateInput($task);
        $task = $this->processTask($task[0]);
        $task->setPriority($priority);

        return $this;
    }

    /**
     * add project from the task to the $projects Collection
     *
     * @param Task $task
     */
    protected function addProject(Task $task)
    {
        if (!$task->projects->isEmpty()) {
            foreach ($task->projects as $project) {

                // check for duplication

                $this->projects->add($project);
            }
        }
    }

    /**
     * add context from the task to the $contexts Collection
     *
     * @param Task $task
     */
    protected function addContext(Task $task)
    {
        if (!$task->contexts->isEmpty()) {
            foreach ($task->contexts as $context) {

                // check for duplication

                $this->contexts->add($context);
            }
        }
    }

    /**
     * add metadata from the task to the $metadata Collection
     +
     * @param Task $task
     */
    protected function addMetadata(Task $task)
    {
        if (!$task->metadata->isEmpty()) {
            foreach ($task->metadata as $meta) {

                // check for duplication

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
     * @return Collection $tasks
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * do a task, move from $todos to $done
     *
     * @param string $id
     */
    public function doTask(string $id): self
    {
        // check if task exists
        $task = $this->getTask($id);
        $task->complete();
        $this->removeTaskFromTodo($task->getId());
        $this->done->add($task);

        return $this;
    }

    /**
     * do all tasks
     */
    public function doAll(): self
    {
        foreach ($this->todo as $task) {
            $this->doTask($task->getId());
        }

        return $this;
    }

    /**
     * undo a task
     *
     * @param string $id
     */
    public function undoTask(string $id): self
    {
        // check if task exists
        $task = $this->getTask($id);
        $task->uncomplete();
        $this->removeTaskFromDone($task->getId());
        $this->todo->add($task);

        return $this;
    }

    /**
     * undo all $done tasks
     */
    public function undoAll(): self
    {
        foreach ($this->done as $task) {
            $this->undoTask($task->getId());
        }

        return $this;
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
        foreach ($this->done as $done) {
            foreach ($done->projects as $project) {
                $doneProjects[] = $project;
            }
            foreach ($done->contexts as $context) {
                $doneProjects[] = $context;
            }
            foreach ($done->metadata as $metadata) {
                $doneProjects[] = $metadata;
            }
        }

        foreach ($this->done as $done) {
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
        foreach ($this->todo as $key => $todo) {
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
        foreach ($this->done as $key => $done) {
            if ($done->getId() === $id) {
                $this->done->delete($key);
            }
        }
    }

    /**
     * get only open tasks
     *
     * @return Collection $todo
     */
    public function getTodo(): Collection
    {
        return $this->todo;
    }

    /**
     * get only done tasks
     *
     * @return Collection $done
     */
    public function getDone(): Collection
    {
        return $this->done;
    }

    /**
     * get only the projects
     *
     * @return Collection $projects
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    /**
     * get only the contexts
     *
     * @return Collection $contexts
     */
    public function getContexts(): Collection
    {
        return $this->contexts;
    }

    /**
     * get only the metadata
     *
     * @return Collection $metadata
     */
    public function getMetadata(): Collection
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

}
