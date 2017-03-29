# todotxt-php
[![Build Status](https://travis-ci.org/dirkolbrich/todotxt-php.svg)](https://travis-ci.org/dirkolbrich/todotxt-php)

todotxt-php is a PHP package to access, handle and validate the content of todo.txt files according to the [todo.txt specification](https://github.com/ginatrapani/todo.txt-cli/wiki/The-Todo.txt-Format) by Gina Trapani.

## Scope

The library will support PHP 7.0+.

The following features are roadmapped:

* Parsing a line separated string into a list, presented as a collection.
* Full unit-testing.
* Ability to sort and filter the tasks.
* Retrieve different lists for todo, done and deleted tasks as separate strings to write these back into separate files.

File reading or writing is not supported. There are a lot of other packages, which handle this task better and it should be the task of the consuming app.

## Quickstart

```php
use TodoTxt;

// read file into string
$file = readFile('/path/to/file.txt');

$todoList = new TodoTxt\TodoList($file);
```

## Collection Structure
```php
TodoList{
    position => $position,
    tasks => ItemList{
        $position,
        $count,
        $list => array[
            [0] => Task{
                $id string
                $raw string
                $task string
                $creationDate Date
                $complete bool
                $completionDate Date
                $due bool
                $dueDate Date
                $priority string
                $projects array([0] => Project, ...)
                $contexts array([0] => Context, ...)
                $metadata array([0] => MetaData, ...)
            },
            // ...
        }
    ),
    todo => array(
        [0] => Task{
            // ...
        },
        // ...
    ),
    done => array(
        [0] => Task{
            // ...
        },
        // ...
    ),
    projects => array(
        [0] => Project{
            $id
            $project
            $tasks array([0] => Task, ...)
        },
        // ...
    ),
    contexts => array(
        [0] => Context{
            $id
            $context
            $tasks array([0] => Task, ...)
        },
        // ...
    ),
    metadata => array(
        [0] => Metadata{
            $id
            $key
            $value
            $tasks array([0] => Task, ...)
        },
        // ...
    ),
};
```

## Function Reference

### `add($task)`

add a task to the list, add to $todo array, add projects, contexts and metadata to corresponding arrays

### `addMultiple($task)`

add multiple tasks to the list, add to $todo array, add projects, contexts and metadata to corresponding arrays

### `addDone($task)`

add a task to the list, mark as done, add to $done array, add projects, contexts and metadata to corresponding arrays

### `addPriority($task, $priority)`

add a task to the list, set a priority, add to $todo array, add projects, contexts and metadata to corresponding arrays

### `do($taskId)`

mark a task as done, remove from $todo array, move to $done array

### `undo($taskId)`

mark a task as open, remove from $done array, move to $todo array

### `due($taskId, $date)`

set a `due:date` metadata to a task, add metadata to corresponding array

### `undue($taskId)`

remove a `due:date` metadata from a task, remove metadata from corresponding array

## Classes Reference

### TodoList.php

variables

```php
$position
$tasks
$todos
$done
$projects
$contexts
$metadata
```

methods

```
__construct()
static make()
splitString($string)
```

public methods

```
add($task)
addMultiple(array $tasks)
addDone($task)
addPriority($task, $priority)
do($task)
doAll()
undo($task)
edit($task)
append($task, $string)
prepend(task, $string)
delete($task)
deleteAll()
prioritize($task, $priority)
unprioritize($task)
deprioritize($task)
due($task)
undue($task)
list($term, $negate)
listAll($term, $negate)
listPrio($priority, $negate)
listProj($project, $negate}
listCon($context, $negate)
listMeta($metadata, $negate)
sort()
sortBy($term)
sortByPrio()
sortByProject()
sortByContext()
sortByDueDate()
filter($term)
archive()
```

private methods

```
```

### Task.php

variables

```php
$id
$raw
$task
$creationDate
$complete
$completionDate
$due
$dueDate
$priority
$projects
$contexts
$metadata
```

public methods

```php
isComplete()
getCompletionDate()
complete()
unComplete()
getCreationDate()
age()
hasPrio()
setPrio($priority)
unsetPrio()
increasePrio()
decreasePrio()
isDue()
setDue($dueDate)
unsetDue()
edit($task)
append($string)
prepend($string)
```

protected methods

```php
createId($task)
parse($task)
findCompleted()
findPriority()
findCreated()
findProject()
findContext()
findMetaData()
findDueDate()
addProject()
addContext()
addMetadata()
rebuildRawString()
```

### Project.php

variables

```php
$id
$project
```

### Context.php

variables

```php
$id
$context
```

### MetaData.php

variables

```php
$id
$key
$value
```

## Program Flow

### Initialisation