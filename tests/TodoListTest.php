<?php

namespace TodoTxt\Tests;

use TodoTxt\TodoList;

class TodoListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Todolist instantiation single string input
     */
    public function testStandardString()
    {
        $task = "This is a task";
        $todolist = new TodoList($task);
        $this->assertCount(1, $todolist->getTasks());
    }

    /**
     * Test Todolist instantiation with new line separated string input
     */
    public function testStandardNewLineString()
    {
        $taskline = "This is a task\nThis is another task\nThis is a third task";
        $todolist = new TodoList($taskline);
        $this->assertCount(3, $todolist->getTasks());
    }

    /**
     * Test Todolist instantiation with array input
     */
    public function testStandardArray()
    {
        $tasks = array(
            "This is a task",
            "This is another task");
        $todolist = new TodoList($tasks);
        $this->assertCount(2, $todolist->getTasks());
    }

    /**
     * Test simple tasks, whitespace trimming and a few edge cases
     */
    public function testAdd()
    {
        $todolist = new TodoList();
        $todolist->add("This is a task");
        $this->assertCount(1, $todolist->getTasks());
        $this->assertInstanceOf("TodoTxt\Task", $todolist->getTask(0));
    }

}
