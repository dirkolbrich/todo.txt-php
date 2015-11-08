<?php

namespace TodoTxt\Tests;

use TodoTxt\TodoList;

class TodoListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Todolist instantiation with different input types
     */
    public function testStandard()
    {
        $task = "This is a task";
        $todolist = new TodoList($task);
        $this->assertCount(1, $todolist->getTasks());

        $tasks = array(
            "This is a task",
            "This is another task");
        $todolist = new TodoList($tasks);
        $this->assertCount(2, $todolist->getTasks());

        // test with new line file
        $taskline = "This is a task\nThis is another task\nThis is yet a third task";
        $todolist = new TodoList($taskline);
        $this->assertCount(3, $todolist->getTasks());

    }    /**
     * Test simple tasks, whitespace trimming and a few edge cases
     */
    public function testAddTask()
    {
        $todolist = new TodoList();
        $todolist->addTask("This is a task");
        $this->assertCount(1, $todolist->getTasks());
        $this->assertInstanceOf("TodoTxt\Task", $todolist->getTask(0));
        $this->assertEquals((string) $todolist->getTask(0)->getTask(), "This is a task");
    }

    public function testAddTasks()
    {
        $todolist = new TodoList();
        $tasks = array(
            "This is a task",
            "This is another task");
        $todolist->addTasks($tasks);
        $this->assertCount(2, $todolist->getTasks());
        $this->assertEquals((string) $todolist->getTasks()[0]->getTask(), "This is a task");
        $this->assertEquals((string) $todolist->getTasks()[1]->getTask(), "This is another task");
    }

    public function testParseTasks()
    {
        $todolist = new TodoList();
        $tasks = "This is a task\nThis is another task\nThis is yet a third task";
        $todolist->parseTasks($tasks);
        $this->assertCount(3, $todolist->getTasks());
        $this->assertEquals((string) $todolist->getTasks()[0]->getTask(), "This is a task");
        $this->assertEquals((string) $todolist->getTasks()[1]->getTask(), "This is another task");
        $this->assertEquals((string) $todolist->getTasks()[2]->getTask(), "This is yet a third task");
    }}
