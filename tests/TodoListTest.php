<?php
declare(strict_types=1);

namespace TodoTxt\Tests;

use TodoTxt\Task;
use TodoTxt\TodoList;
use PHPUnit\Framework\TestCase;


class TodoListTest extends TestCase
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
     * Test adding a simple tasks
     */
    public function testAddWithClass()
    {
        $task = new Task("This is a task");
        $todolist = new TodoList();
        $todolist->addTask($task);
        $this->assertCount(1, $todolist->getTasks());
        $this->assertInstanceOf("TodoTxt\Task", $todolist->getTasks()[0]);
    }

    /**
     * Test adding a simple tasks
     */
    public function testAddWithString()
    {
        $todolist = new TodoList();
        $todolist->addTask("This is a task");
        $this->assertCount(1, $todolist->getTasks());
        $this->assertInstanceOf("TodoTxt\Task", $todolist->getTasks()[0]);
    }

    /**
     * Test adding multiple mixed tasks
     */
    public function testAddMultipleMixed()
    {
        $task = new Task("This is a task");
        $todolist = new TodoList();
        $todolist->addMultiple(array($task, 'Another task'));
        $this->assertCount(2, $todolist->getTasks());
    }

}
