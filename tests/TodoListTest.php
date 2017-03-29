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

        $this->assertEquals(1, $todolist->getTasks()->count());
    }

    /**
     * Test Todolist instantiation with new line separated string input
     */
    public function testStandardNewLineString()
    {
        $taskline = "This is a task\nThis is another task\nThis is a third task";
        $todolist = new TodoList($taskline);

        $this->assertEquals(3, $todolist->getTasks()->count());
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
     * Test Todolist with multiple different projects
     */
    public function testMultipleProjects()
    {
        $tasks = array(
            "This is a task with a +project",
            "This is another task with +anotherproject");
        $todolist = new TodoList($tasks);

        $this->assertCount(2, $todolist->getProjects());
    }

    /**
     * Test Todolist with multiple different projects
     */
    public function testMultipleIdenticalProjects()
    {
        $tasks = array(
            "This is a task with a +project",
            "This is another task with an identical +project");
        $todolist = new TodoList($tasks);

        $this->assertCount(1, $todolist->getProjects());
    }

    /**
     * Test Todolist with multiple different contexts
     */
    public function testMultipleContexts()
    {
        $tasks = array(
            "This is a task with a @context",
            "This is another task with @anothercontext");
        $todolist = new TodoList($tasks);

        $this->assertCount(2, $todolist->getContexts());
    }

    /**
     * Test Todolist with multiple different contexts
     */
    public function testMultipleIdenticalContexts()
    {
        $tasks = array(
            "This is a task with a +context",
            "This is another task with an identical +context");
        $todolist = new TodoList($tasks);

        $this->assertCount(1, $todolist->getContexts());
    }

    /**
     * Test Todolist with multiple different metadata
     */
    public function testMultipleMetadata()
    {
        $tasks = array(
            "This is a task with a meta:data",
            "This is another task with another:meta");
        $todolist = new TodoList($tasks);

        $this->assertCount(2, $todolist->getMetadata());
    }

    /**
     * Test Todolist with multiple different metadata
     */
    public function testMultipleIdenticalMetadata()
    {
        $tasks = array(
            "This is a task with a meta:data",
            "This is another task with an identical meta:data");
        $todolist = new TodoList($tasks);

        $this->assertCount(1, $todolist->getMetadata());
    }

    /**
     * Test adding a simple tasks
     */
    public function testAddWithClass()
    {
        $taskMock = $this->createMock('TodoTxt\Task');

        // $task = new Task("This is a task");
        $todolist = new TodoList();
        $todolist->addTask($taskMock);

        $this->assertEquals(1, $todolist->getTasks()->count());
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
        // mock Task class
        $taskMock = $this->createMock('TodoTxt\Task');

        $todolist = new TodoList();
        $todolist->addMultiple(array($taskMock, 'Another task'));

        $this->assertCount(2, $todolist->getTasks());
    }

    /**
     * Test adding and completing a task
     */
    public function testAddDone()
    {
        $todolist = new TodoList();
        $todolist->addDone("This is a task");

        $this->assertCount(1, $todolist->getTasks());
        $this->assertCount(1, $todolist->getDone());
        $this->assertTrue($todolist->getTasks()->first()->isComplete());
    }

    /**
     * Test adding a task and setting priority
     */
    public function testAddPriority()
    {
        $todolist = new TodoList();
        $todolist->addPriority("This is a task", 'A');

        $this->assertCount(1, $todolist->getTasks());
        $this->assertTrue($todolist->getTasks()->first()->hasPriority());
        $this->assertEquals('A', $todolist->getTasks()->first()->getPriority());
    }

    /**
     * Test completing a task
     */
    public function testDoTask()
    {
        $todolist = new TodoList("This is a task");
        $todolist->doTask($todolist->getTasks()->first()->getId());

        $this->assertCount(1, $todolist->getTasks());
        $this->assertCount(0, $todolist->getTodo());
        $this->assertCount(1, $todolist->getDone());
        $this->assertTrue($todolist->getTasks()->first()->isComplete());
    }

    /**
     * Test completing all tasks
     */
    public function testDoAll()
    {
        $tasks = array(
            "This is a task",
            "This is another task",
            "This is a third task"
            );

        $todolist = new TodoList($tasks);
        $todolist->doAll();

        $this->assertCount(3, $todolist->getTasks());
        $this->assertCount(0, $todolist->getTodo());
        $this->assertCount(3, $todolist->getDone());
        $this->assertTrue($todolist->getTasks()->first()->isComplete());
        $this->assertTrue($todolist->getTasks()->last()->isComplete());
    }

    /**
     * Test undoing a task
     */
    public function testUndoTask()
    {
        $todolist = new TodoList("x 2017-03-23 This is a completed task");
        $todolist->undoTask($todolist->getDone()->first()->getId());

        $this->assertFalse($todolist->getTasks()->first()->isComplete());
        $this->assertCount(1, $todolist->getTodo());
        $this->assertCount(0, $todolist->getDone());
    }

    /**
     * Test deleting a task
     */
    public function testDeleteTask()
    {
        $tasks = array(
            "This is a task",
            "This is another task",
            "This is a third task"
            );

        $todolist = new TodoList($tasks);
        $todolist->deleteTask($todolist->getTasks()->first()->getId());

        $this->assertCount(2, $todolist->getTasks());
    }

    /**
     * Test archiving done tasks
     */
    public function testArchive()
    {
        $tasks = array(
            "This is a task",
            "This is another task",
            "x 2017-03-27 This is completed task with +project +secondproject @context @secondcontext meta:data  another:meta",
            "This is a third task with a +project",
            "This is a fourth task with a @context",
            "This is a fifth task with a meta:data"
            );

        $todolist = new TodoList($tasks);
        // var_dump($todolist->getProjects());
        // var_dump($todolist->getContexts());
        // var_dump($todolist->getMetadata());
        $todolist = $todolist->archive();
        // var_dump($todolist->getProjects());
        // var_dump($todolist->getContexts());
        // var_dump($todolist->getMetadata());

        $this->assertCount(5, $todolist->getTasks());
        $this->assertCount(5, $todolist->getTodo());
        $this->assertCount(0, $todolist->getDone());
        $this->assertCount(1, $todolist->getProjects());
        $this->assertCount(1, $todolist->getContexts());
        $this->assertCount(1, $todolist->getMetadata());
    }
}
