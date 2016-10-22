#todotxt-php
[![Build Status](https://travis-ci.org/dirkolbrich/todo.txt-php.svg)](https://travis-ci.org/dirkolbrich/todo.txt-php)

todotxt-php is a PHP package to access, handle and validate the content of todo.txt files according to the [todo.txt specification](https://github.com/ginatrapani/todo.txt-cli/wiki/The-Todo.txt-Format) by Gina Trapani.

##Scope

The library will support PHP 5.6+.

The following features are roadmapped:

* Parsing a line separated string into a list, presented as a collection.
* Full unit-testing.
* Ability to sort and filter the tasks.
* Retrieve different lists for todo, done and deleted tasks as separate strings to write these back into separate files.

File loading or writing is not supported. There are a lot of other packages, which handle this task better and it should be the task of the consuming app (separation of concern).

##Quickstart

```php
use TodoTxt;

// read file into string
$file = readFile('/path/to/file.txt');

$todoList = new TodoList($file);
```

##Collection Structure
```php
TodoList{
    position => $position,
    tasks(
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
            $projects array()
            $contexts array()
            $metadata array()
        },
        // ...
    ),
    todo(
        [0] => Task{
            // ...
        },
        // ...
    ),
    done(
        [0] => Task{
            // ...
        },
        // ...
    ),
    projects(
        [0] => Project{
            $id
            $project
            $tasks array()
        },
        // ...
    ),
    contexts(
        [0] => Context{
            $id
            $context
            $tasks array()
        },
        // ...
    ),
    metadata(
        [0] => Metadata{
            $id
            $key
            $value
            $tasks array()
        },
        // ...
    ),
};
```


##Function Reference

###TodoList.php

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

`__construct()`
`static make()`
`splitString($string)`

public methods

```php
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

```php
```

###Task.php

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

###Project.php

variables

```php
$id
$project
```

###Context.php

variables

```php
$id
$context
```

###MetaData.php

variables

```php
$id
$key
$value
```

##Program Flow

### Initialisation