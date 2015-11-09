# todo.txt-php
[![Build Status](https://travis-ci.org/dirkolbrich/todo.txt-php.svg)](https://travis-ci.org/dirkolbrich/todo.txt-php)

todo.txt-php is a PHP package to access, handle validte the content of todo.txt files. 

## Scope

The library will support PHP 5.4+.

Parsing errors should be silent (and should resort to the offending text
being represented as is where possible). However other errors should
result in exceptions.

The following features are roadmapped:

*   Parsing a task into a model from a text string.
*   Full unit-testing.
*   Ability to sort and filter the tasks.

File loading or writing is not included, there are a lot of other packages, which handle this way better.

## Quickstart


