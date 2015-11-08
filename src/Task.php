<?php

namespace TodoTxt;

// use Project;
// use Context;
use TodoTxt\Exceptions\EmptyStringException;
use TodoTxt\Exceptions\CompletionParadoxException;
use TodoTxt\Exceptions\CannotCalculateAgeException;

/**
 * Encapsulates a single line of a todo.txt list.
 * Handles the parsing of contexts, projects and other info from a task.
 *
 * @TODO: Make the find* methods public static?
 * @TODO: Devise a good way to write plug-ins for this process. Possibly
 *        by simply extending the class.
 * @TODO: Make a ContextList and ProjectList class to hold contexts and
 *        projects (so we can do count($list->projects) etc.).
 */
class Task
{
    /**
     * The task as passed to the constructor
     *
     * @var string
     */
    protected $rawTask;
    
    /**
     * The task, sans priority, completion marker/date
     *
     * @var string
     */
    protected $task;
    
    /**
     * @var bool
     */
    protected $complete = false;

    /**
     * @var \DateTime
     */
    protected $completionDate;
    
    /**
     * A single-character, uppercase priority, if found
     *
     * @var string
     */
    protected $priority;
    
    /**
     * The date the task was created
     *
     * @var \DateTime
     */
    protected $creationDate;
    
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
     * @see __get
     * @see __set
     */
    protected $metadata = array();

    /**
     * Create a new task from a raw line held in a todo.txt file.
     * @param string $task A raw task line
     * @throws \EmptyStringException When $task is an empty string (or whitespace)
     */
    public function __construct($task)
    {
        $task = trim($task);
        if (strlen($task) == 0) {
            throw new EmptyStringException;
        }
        $this->rawTask = $task;
        
        // Since each of these parts can occur sequentially and only at
        // the start of the string, pass the remainder of the task on.
        $result = $this->findCompleted($task);
        $result = $this->findPriority($result);
        $result = $this->findCreated($result);
        $this->task = $result;
        
        // Find metadata held in the rest of the task
        $this->findContexts($result);
        $this->findProjects($result);
        $this->findMetadata($result);
    }
    
    /**
     * Returns the age of the task if the task has a creation date.
     *
     * @param \DateTime|string $endDate  - The end-date to use if the task
     * does not have a completion date. If this is null and the task
     * doesn't have a completion date the current date will be used.
     * @return \DateInterval  - the age of the task.
     * @throws \CannotCalculateAgeException - If the task does not have a creation date
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
     * Add an array of projects to the list.
     * Using this method will prevent duplication in the array.
     * @param array $projects Array of project names.
     */
    public function addProjects(array $projects)
    {
        $projects = array_map("trim", $projects);
        foreach ($projects as $project) {
            $this->projects[] = new Project($project);
        }
        // $this->projects = array_unique(array_merge($this->projects, $projects));
    }
    
    /**
     * Add an array of contexts to the list.
     * Using this method will prevent duplication in the array.
     * @param array $contexts Array of context names.
     */
    public function addContexts(array $contexts)
    {
        $contexts = array_map("trim", $contexts);
        foreach ($contexts as $context) {
            $this->contexts[] = new Context($context);
        }
        // $this->contexts = array_unique(array_merge($this->contexts, $contexts));
    }
    
    /**
     * Access meta-properties, as held by key:value metadata in the task.
     * @param string $name The name of the meta-property.
     * @return string Value if property found, or null.
     */
    public function __get($name)
    {
        return isset($this->metadata[$name]) ? $this->metadata[$name] : null;
    }
    
    /**
     * Check for existence of a meta-property.
     *
     * @param string $name The name of the meta-property.
     * @return boolean Whether the property is contained in the task.
     */
    public function __isset($name)
    {
        return isset($this->metadata[$name]);
    }
    
    /**
     * Re-build the task string.
     *
     * @return string The task as a todo.txt line.
     */
    public function __toString()
    {
        $task = '';
        if ($this->isCompleted) {
            $task .= sprintf('x %s ', $this->completionDate->format("Y-m-d"));
        }
        
        if (isset($this->priority)) {
            $task .= sprintf('(%s) ', strtoupper($this->priority));
        }
        
        if (isset($this->creationDate)) {
            $task .= sprintf('%s ', $this->created->format("Y-m-d"));
        }
        
        $task .= $this->task;

        return $task;
    }
    
    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->complete;
    }
    
    /**
     * @return \DateTime
     */
    public function getCompletionDate()
    {
        return $this->isCompleted() && isset($this->completionDate) ? $this->completionDate : null;
    }
    
    /**
     * @return \DateTime|null
     */
    public function getCreationDate()
    {
        return isset($this->creationDate) ? $this->creationDate : null;
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
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
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
     * Find a creation date (after a priority marker).
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
     * Find @contexts within the task
     *
     * @param string $input Input string to check
     */
    protected function findContexts($input)
    {
        // Match an at-sign, any non-whitespace character, ending with
        // an alphanumeric or underscore, followed either by the end of
        // the string or by whitespace.
        $pattern = "/@(\S+\w)(?=\s|$)/";
        if (preg_match_all($pattern, $input, $matches) > 0) {
            $this->addContexts($matches[1]);
        }
    }
    
    /**
     * Find +projects within the task
     * @param string $input Input string to check
     */
    protected function findProjects($input)
    {
        // The same rules as contexts, except projects use a plus.
        $pattern = "/\+(\S+\w)(?=\s|$)/";
        if (preg_match_all($pattern, $input, $matches) > 0) {
            $this->addProjects($matches[1]);
        }
    }
    
    /**
     * Metadata can be held in the string in the format key:value.
     * This is usually used by add-ons, which provide their own
     * formatting rules for tasks.
     * This data can be accessed using __get() and __isset().
     *
     * @param string $input Input string to check
     * @see __get
     * @see __set
     */
    protected function findMetadata($input)
    {
        // Match a word (alphanumeric+underscores), a colon, followed by
        // any non-whitespace character.
        $pattern = "/(?<=\s|^)(\w+):(\S+)(?=\s|$)/";
        if (preg_match_all($pattern, $input, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $this->metadata[$match[1]] = $match[2];
            }
        }
    }
}
