# todo.txt-php


todo.txt-php is a PHP library to access todo.txt files, validate them
and handle saving them to file.

## Scope

The library will support PHP 5.4+.

Parsing errors should be silent (and should resort to the offending text
being represented as is where possible). However other errors should
result in exceptions.

The following features are roadmapped:

*   Task manipulation via a model.
*   Parsing a task into a model from a text string.
*   Full unit-testing.
*   Ability to sort and filter the tasks.

File loading or writing is not included, there are a lot of packages, which handle this way better

## Quickstart


