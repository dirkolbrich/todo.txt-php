<?php

namespace TodoTxt;

use TodoTxt\Exceptions\EmptyTaskException;
use TodoTxt\Exceptions\EmptyStringException;
use TodoTxt\Exceptions\InvalidStringException;
use TodoTxt\Exceptions\CompletionParadoxException;
use TodoTxt\Exceptions\CannotCalculateAgeException;

/**
 * Encapsulates a single line of a todo.txt list.
 * Handles the parsing of contexts, projects and other info from a task.
 */
class Task
{
    /**
     * The md5 hash of the raw utf-8 encoded string
     *
     * var string
     */
    protected $id;

    /**
     * The task as passed to the constructor
     *
     * @var string
     */
    protected $raw;

    /**
     * The task, sans priority, completion marker/date
     *
     * @var string
     */
    protected $task;

      /**
     * The date the task was created
     *
     * @var \DateTime
     */
    protected $creationDate;

    /**
     * @var bool
     */
    protected $complete = false;

    /**
     *
     * The date the task is completed
     *
     * @var \DateTime
     */
    protected $completionDate;

    /**
     * @var bool
     */
    protected $due = false;

    /**
     * The date the task is due
     *
     * @var \DateTime
     */
    protected $dueDate;

    /**
     * A single-character, uppercase priority, if found
     *
     * @var string
     */
    protected $priority;
        
    /**
     * A list of project names found (case-sensitive)
     *
     * @var array
     */
    public $projects = array();
    
    /**
     * A list of context names found (case-sensitive)
     *
     * @var array
     */
    public $contexts = array();

    /**
     * A map of meta-data, contained in the task
     *
     * @var array
     */
    public $metadata = array();

    /**
     * Create a new task from a raw line held in a todo.txt file.
     *
     * @param string $string A raw task line
     * @throws \EmptyStringException When $task is an empty string (or whitespace)
     */
    public function __construct($string)
    {
        $this->id = $this->createId($string);
        
        $task = trim($string);
        if (strlen($task) == 0) {
            throw new EmptyStringException;
        }
        $this->parse($task);
    }
    
    /**
     * create the $id of the task, md5 hash based on the utf-8 encoded raw string
     *
     * @param string $string
     * @return string
     */
    protected function createId($string) {
        return md5(utf8_encode($string));
    } 

    /**
     * Parse the raw task string into its components
     *
     * @param string $task
     * @throws EmptyTaskException
     */
    protected function parse($task)
    {
        $this->raw = $task;
        
        // Since each of these parts can occur sequentially and only at
        // the start of the string, pass the remainder of the task on.
        $result = $this->findCompleted($task);
        $result = $this->findPriority($result);
        $result = $this->findCreated($result);
        
        // validate rest of string if task exists
        $result = trim($result);
        if (strlen($result) == 0) {
            throw new EmptyTaskException;
        }
        $this->task = $result;
        
        // Find metadata held in the rest of the task
        $this->findProject($result);
        $this->findContext($result);
        $this->findMetadata($result);
        $this->findDue($result);
    }

    /**
     * Looks for a "x " marker, followed by a date.
     *
     * Complete tasks start with an X (case-insensitive), followed by a
     * space. The date of completion follows this (required).
     * Dates are formatted like YYYY-MM-DD.
     *
     * @param string $input String to check for completion.
     * @return string Returns the rest of the task, without this part.
     */
    protected function findCompleted($input)
    {
        // Match a lower or uppercase X, followed by a space and a
        // YYYY-MM-DD formatted date, followed by another space.
        // Invalid dates can be caught but checked after.
        $pattern = "/^(X|x) (\d{4}-\d{2}-\d{2}) /";

        if (preg_match($pattern, $input, $matches) == 1) {
            // Rather than throwing exceptions around, silently bypass this
            try {
                $this->completionDate = new \DateTime($matches[2]);
            } catch (\Exception $e) {
                return $input;
            }
            
            $this->complete = true;
            return substr($input, strlen($matches[0]));
        }
        return $input;
    }

    /**
     * Find a priority marker.
     * Priorities are signified by an uppercase letter in parentheses.
     *
     * @param string $input Input string to check.
     * @return string Returns the rest of the task, without this part.
     */
    protected function findPriority($input)
    {
        // Match one uppercase letter in brackers, followed by a space.
        $pattern = "/^\(([A-Z])\) /";
        if (preg_match($pattern, $input, $matches) == 1) {
            $this->priority = $matches[1];
            return substr($input, strlen($matches[0]));
        }
        return $input;
    }

     /**
     * Find a creation date.
     *
     * @param string $input Input string to check.
     * @return string Returns the rest of the task, without this part.
     */
    protected function findCreated($input)
    {
        // Match a YYYY-MM-DD formatted date, followed by a space.
        // Invalid dates can be caught but checked after.
        $pattern = "/^(\d{4}-\d{2}-\d{2}) /";
        if (preg_match($pattern, $input, $matches) == 1) {
            // Rather than throwing exceptions around, silently bypass this
            try {
                $this->creationDate = new \DateTime($matches[1]);
            } catch (\Exception $e) {
                return $input;
            }
            return substr($input, strlen($matches[0]));
        }
        return $input;
    }

    /**
     * Find +projects within the task
     *
     * @param string $input Input string to check
     */
    protected function findProject($input)
    {
        // Match an + sign, any non-whitespace character, ending with
        // an alphanumeric or underscore, followed either by the end of
        // the string or by whitespace.
        $pattern = "/\+(?P<project>\S+\w)(?=\s|$)/";
        if (preg_match_all($pattern, $input, $matches) > 0) {
            $this->addProject($matches['project']);
        }
    }

    /**
     * Find @contexts within the task
     *
     * @param string $input Input string to check
     */
    protected function findContext($input)
    {
        // Match an at-sign, any non-whitespace character, ending with
        // an alphanumeric or underscore, followed either by the end of
        // the string or by whitespace.
        $pattern = "/@(?P<context>\S+\w)(?=\s|$)/";
        if (preg_match_all($pattern, $input, $matches) > 0) {
            $this->addContext($matches['context']);
        }
    }

    /**
     * Metadata can be held in the string in the format key:value.
     * This is usually used by add-ons, which provide their own
     * formatting rules for tasks.
     *
     * @param string $input Input string to check
     */
    protected function findMetadata($input)
    {
        // Match a word (alphanumeric+underscores), a colon, followed by
        // any non-whitespace character.
        $pattern = "/(?P<key>\w+):(?P<value>\S+)(?=\s|$)/";
        if (preg_match_all($pattern, $input, $matches, PREG_SET_ORDER) > 0) {
            $this->addMetadata($matches);
        }
    }

    /**
     * Find a due date within the metadata.
     *
     * @return void
     */
    protected function findDue()
    {
        foreach ($this->metadata as $meta) {
            if ($meta->key == 'due') {
                $this->due = true;
                $this->dueDate = new \DateTime($meta->value);
                return;
            }
        }
    }

    /**
     * Add an array of projects to the list.
     * Using this method will prevent duplication in the array.
     *
     * @param array $projects - Array of project names.
     */
    protected function addProject(array $projects)
    {
        $projects = array_map("trim", $projects);
        foreach ($projects as $project) {
            $this->projects[] = $project;
        }
        $this->projects = array_unique(array_merge($this->projects, $projects));
    }
    
    /**
     * Add an array of contexts to the list.
     * Using this method will prevent duplication in the array.
     *
     * @param array $contexts - Array of context names.
     */
    protected function addContext(array $contexts)
    {
        $contexts = array_map("trim", $contexts);
        foreach ($contexts as $context) {
            $this->contexts[] = $context;
        }
        $this->contexts = array_unique(array_merge($this->contexts, $contexts));
    }

    /**
     * Add an array of metadata to the list.
     *
     * @param array $metadatas - Array of metadata keys and values.
     */
    protected function addMetadata(array $metadatas)
    {
        foreach ($metadatas as $metadata) {
            $this->metadata[$metadata['key']] = $metadata['value'];
        }
    }

    /**
     * @return bool
     */
    public function isComplete()
    {
        return $this->complete;
    }

    /**
     * @return \DateTime|null
     */
    public function getCompletionDate()
    {
        return $this->isComplete() && isset($this->completionDate) ? $this->completionDate : null;
    }

    /**
     * set task to complete
     */
    public function complete()
    {
        $this->complete = true;
        $this->completionDate = new \DateTime("now");
        $this->raw = $this->rebuildRawString();
    }

    /**
     * set task to uncomplete
     */
    public function uncomplete()
    {
        $this->complete = false;
        $this->raw = $this->rebuildRawString();
    }
    
    /**
     * @return \DateTime|null
     */
    public function getCreationDate()
    {
        return isset($this->creationDate) ? $this->creationDate : null;
    }

    /**
     * Returns the age of the task if the task has a creation date.
     *
     * @param \DateTime|string $endDate  - The end-date to use if the task
     * does not have a completion date. If this is null and the task
     * doesn't have a completion date the current date will be used.
     * @return \DateInterval  - the age of the task.
     * @throws \CannotCalculateAgeException - If the task does not have a creation date
     * @throws \CompletionParadoxException - If the task is completed before the creation date
     */
    public function age($endDate = null)
    {
        if (!isset($this->creationDate)) {
            throw new CannotCalculateAgeException;
        }
        
        // Decide on an end-date to use - completionDate, then a
        // provided date, then the current date.
        $end = new \DateTime('now');
        if (isset($this->completionDate)) {
            $end = $this->completionDate;
        } elseif (!is_null($endDate)) {
            if (!($endDate instanceof \DateTime)) {
                $endDate = new \DateTime($endDate);
            }
            $end = $endDate;
        }
        
        $diff = $this->creationDate->diff($end);
        if ($diff->invert) {
            throw new CompletionParadoxException;
        }
        
        return $diff;
    }

    /**
     * @return bool
     */
    public function hasPrio()
    {
        if (isset($this->priority)) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getPrio()
    {
        return $this->priority;
    }

    /**
     * set priority of a task, overrides old $priority
     *
     * @param string $priority
     * @throws InvalidStringException
     */
    public function setPrio($priority)
    {
        if (!ctype_alpha($priority) && !ctype_upper($priority)) {
            throw new InvalidStringException;
        }
        $this->priority = $priority;
        $this->raw = $this->rebuildRawString();
    }

    /**
     * unset $priority of task
     */
    public function unsetPrio()
    {
        $this->priority = null;
        $this->raw = $this->rebuildRawString();
    }

    /**
     * increase $priority of task
     *
     * @param integer $step
     */
    public function increasePrio($step = 1)
    {
        // get $priority and set it one higher
        do {
            // if Priority already at highest
            if ($this->priority === 'A') {
                return;
            }
            ++$this->priority;
            $step--;
        } while ($step > 0);
        
        $this->raw = $this->rebuildRawString();
    }

    /**
     * decrease $priority of task
     *
     * @param integer $step
     */
    public function decreasePrio($step = 1)
    {
        // get $priority and set it one higher
        do {
            // if Priority already at lowest
            if ($this->priority === 'Z') {
                return;
            }
            --$this->priority;
            $step--;
        } while ($step > 0);
        
        $this->raw = $this->rebuildRawString();
    }

    /**
     * @return bool
     */
    public function isDue()
    {
        return $this->due;
    }
    
    /**
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->isDue() && isset($this->dueDate) ? $this->dueDate : null;
    }

    /**
     * Get the id of the task
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the remainder of the task (sans completed marker, creation date and priority)
     *
     * @return string
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * edit the complete task
     *
     * @param string $string
     */
    public function edit($string)
    {
        $task = trim($string);
        if (strlen($task) == 0) {
            throw new EmptyStringException;
        }
        $this->parse($task);
    }
    
    /**
     * append $string to end of task
     *
     * @param string $string
     */
    public function append($string)
    {
        // Find metadata held in the appended string
        $this->findProject($string);
        $this->findContext($string);
        $this->findMetadata($string);
        $this->findDue($string);
        
        $this->task .= $string;
        $this->raw = $this->rebuildRawString();
    }

    /**
     * prepend $string to beginning of task, but after any completion or creation markers
     *
     * @param string $string
     */
    public function prepend($string)
    {
        // Find metadata held in the prepended string
        $this->findProject($string);
        $this->findContext($string);
        $this->findMetadata($string);
        $this->findDue($string);
        
        $this->task = $string . $this->task;
        $this->raw = $this->rebuildRawString();
    }

    /**
     * Re-build the raw task string.
     *
     * @return string The task as a todo.txt line.
     */
    public function rebuildRawString()
    {
        $raw = '';
        if ($this->isComplete()) {
            $raw .= sprintf('x %s ', $this->completionDate->format("Y-m-d"));
        }
        
        if (isset($this->priority)) {
            $raw .= sprintf('(%s) ', strtoupper($this->priority));
        }
        
        if (isset($this->creationDate)) {
            $raw .= sprintf('%s ', $this->creationDate->format("Y-m-d"));
        }
        
        $raw .= $this->task;

        return $raw;
    }
    
    /**
     * Re-build the task string.
     *
     * @return string - The task as a todo.txt line.
     */
    public function __toString()
    {
        $task = $this->rebuildRawString();
        return $task;
    }
}
