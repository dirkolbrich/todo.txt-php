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

##Function Reference

###TodoList.php

`$tasks`
`$todos`
`$done`
`$projects`
`$contexts`
`$metadata`

`__construct()`
`static make()`
`splitString($string)`
`addTask()`
`getTask()`
`updateTask()`
`deleteTask()`
`completeTask()`
`sort()`
`sortBy()`
`sortByPriority()`
`sortByProject()`
`sortByContext()`
`sortByDueDate()`

###Task.php

`$raw`
`$task`
`$creationDate`
`$completionDate`
`$complete`
`$priority`
`$project`
`$context`
`$metadata`

`create()`
`read()`
`update()`
`delete()`
`isComplete()`
`complete()`
`uncomplete()`
`hasPriority()`
`setPriority()`
`unsetPriority()`
`increasePriority()`
`decreasePriority()`
`isDue()`
`setDue()`
`unsetDue()`
`age()`

`parseTask()`
`findCompleted()`
`findPriority()`
`findCreated()`
`findProject()`
`findContext()`
`findMetaData()`
`findDueDate()`


###Project.php

`$project`

###Context.php

`$context`

###MetaData.php

`$key`
`$value`

