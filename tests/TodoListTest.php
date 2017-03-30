<?php
declare(strict_types=1);

namespace TodoTxt\Tests;

use TodoTxt\Task;
use TodoTxt\TodoList;
use PHPUnit\Framework\TestCase;


class TodoListTest extends TestCase
{
    /**
     * Test TodoList instantiation
     */
    public function testInstantiation()
    {
        $list = new TodoList();

        $this->assertInstanceOf("TodoTxt\TodoList", $list);
        $this->assertEquals(0, $list->getTasks()->count());

    }

    /**
     * Test static TodoList instantiation
     */
    public function testStatic()
    {
        $list = TodoList::make();

        $this->assertInstanceOf("TodoTxt\TodoList", $list);
    }

    /**
     * Test static TodoList instantiation
     */
    public function testStaticWithString()
    {
        $list = TodoList::withString('test');
        $this->assertInstanceOf("TodoTxt\TodoList", $list);
    }

    /**
     * Test static TodoList instantiation
     */
    public function testStaticWithArray()
    {
        $array = array('test', 'another test');
        $list = TodoList::withArray($array);
        $this->assertInstanceOf("TodoTxt\TodoList", $list);
    }

    /**
     * Test static TodoList instantiation with class
     */
    public function testStaticWithTask()
    {
        $task = new Task('test');
        $list = TodoList::withTask($task);
        $this->assertInstanceOf("TodoTxt\TodoList", $list);
    }

    /**
     * Test Todolist instantiation single string input
     */
    public function testStandardWithString()
    {
        $task = "This is a task";
        $list = new TodoList($task);
        $this->assertEquals(1, $list->getTasks()->count());
    }

    /**
     * Test Todolist instantiation with new line separated string input
     */
    public function testStandardWithNewLineString()
    {
        $string = "This is a task\nThis is another task\nThis is a third task";
        $list = new TodoList($string);

        $this->assertEquals(3, $list->getTasks()->count());
    }

    /**
     * Test Todolist instantiation with array input
     */
    public function testStandardWithArray()
    {
        $tasks = array(
            "This is a task",
            "This is another task");
        $list = new TodoList($tasks);

        $this->assertCount(2, $list->getTasks());
    }

    /**
     * Test Todolist instantiation with array input
     */
    public function testStandardWithClass()
    {
        $task = new Task('This is a task');
        $list = new TodoList($task);

        $this->assertCount(1, $list->getTasks());
    }

    /**
     * Test adding a simple tasks
     */
    public function testAddTaskWithString()
    {
        $list = new TodoList();
        $list->addTask("This is a task");

        $this->assertCount(1, $list->getTasks());
        $this->assertInstanceOf("TodoTxt\Task", $list->getTasks()[0]);
    }

    /**
     * Test adding a simple tasks
     */
    public function testAddTaskWithNewLineString()
    {
        $string = "This is a task\nThis is another task\nThis is a third task";
        $list = new TodoList();
        $list->addTask($string);

        $this->assertCount(3, $list->getTasks());
        $this->assertInstanceOf("TodoTxt\Task", $list->getTasks()[0]);
    }

    /**
     * Test adding a simple tasks
     */
    public function testAddTaskWithClass()
    {
        $task = new Task('test');

        // $task = new Task("This is a task");
        $list = new TodoList();
        $list->addTask($task);

        $this->assertEquals(1, $list->getTasks()->count());
        $this->assertInstanceOf("TodoTxt\Task", $list->getTasks()[0]);
    }


    /**
     * Test adding multiple mixed tasks
     */
    public function testAddMultipleTasksMixed()
    {
        $task = new Task('test');

        $list = new TodoList();
        $list->addMultipleTasks(array($task, 'Another task'));

        $this->assertCount(2, $list->getTasks());
    }

    /**
     * Test adding and completing a task
     */
    public function testAddDone()
    {
        $list = new TodoList();
        $list->addDone("This is a task");

        $this->assertCount(1, $list->getTasks());
        $this->assertCount(1, $list->getDone());
        $this->assertTrue($list->getTasks()->first()->isComplete());
    }

    /**
     * Test adding a task and setting priority
     */
    public function testAddPriority()
    {
        $list = new TodoList();
        $list->addPriority("This is a task", 'A');

        $this->assertCount(1, $list->getTasks());
        $this->assertTrue($list->getTasks()->first()->hasPriority());
        $this->assertEquals('A', $list->getTasks()->first()->getPriority());
    }


    /**
     * Test Todolist with multiple different projects
     */
    public function testMultipleProjects()
    {
        $tasks = array(
            "This is a task with a +project",
            "This is another task with +anotherproject");
        $list = new TodoList($tasks);

        $this->assertCount(2, $list->getProjects());
    }

    /**
     * Test Todolist with multiple different projects
     */
    public function testMultipleIdenticalProjects()
    {
        $tasks = array(
            "This is a task with a +project",
            "This is another task with an identical +project");
        $list = new TodoList($tasks);

        $this->assertCount(1, $list->getProjects());
    }

    /**
     * Test Todolist with multiple different contexts
     */
    public function testMultipleContexts()
    {
        $tasks = array(
            "This is a task with a @context",
            "This is another task with @anothercontext");
        $list = new TodoList($tasks);

        $this->assertCount(2, $list->getContexts());
    }

    /**
     * Test Todolist with multiple different contexts
     */
    public function testMultipleIdenticalContexts()
    {
        $tasks = array(
            "This is a task with a +context",
            "This is another task with an identical +context");
        $list = new TodoList($tasks);

        $this->assertCount(1, $list->getContexts());
    }

    /**
     * Test Todolist with multiple different metadata
     */
    public function testMultipleMetadata()
    {
        $tasks = array(
            "This is a task with a meta:data",
            "This is another task with another:meta");
        $list = new TodoList($tasks);

        $this->assertCount(2, $list->getMetadata());
    }

    /**
     * Test Todolist with multiple different metadata
     */
    public function testMultipleIdenticalMetadata()
    {
        $tasks = array(
            "This is a task with a meta:data",
            "This is another task with an identical meta:data");
        $list = new TodoList($tasks);

        $this->assertCount(1, $list->getMetadata());
    }

    /**
     * Test completing a task
     */
    public function testDoTask()
    {
        $list = new TodoList("This is a task");
        $list->doTask($list->getTasks()->first()->getId());

        $this->assertCount(1, $list->getTasks());
        $this->assertCount(0, $list->getTodo());
        $this->assertCount(1, $list->getDone());
        $this->assertTrue($list->getTasks()->first()->isComplete());
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

        $list = new TodoList($tasks);
        $list->doAll();

        $this->assertCount(3, $list->getTasks());
        $this->assertCount(0, $list->getTodo());
        $this->assertCount(3, $list->getDone());
        $this->assertTrue($list->getTasks()->first()->isComplete());
        $this->assertTrue($list->getTasks()->last()->isComplete());
    }

    /**
     * Test undoing a task
     */
    public function testUndoTask()
    {
        $list = new TodoList("x 2017-03-23 This is a completed task");
        $list->undoTask($list->getDone()->first()->getId());

        $this->assertFalse($list->getTasks()->first()->isComplete());
        $this->assertCount(1, $list->getTodo());
        $this->assertCount(0, $list->getDone());
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

        $list = new TodoList($tasks);
        $list->deleteTask($list->getTasks()->first()->getId());

        $this->assertCount(2, $list->getTasks());
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

        $list = new TodoList($tasks);
        // var_dump($list->getProjects());
        // var_dump($list->getContexts());
        // var_dump($list->getMetadata());
        $list = $list->archive();
        // var_dump($list->getProjects());
        // var_dump($list->getContexts());
        // var_dump($list->getMetadata());

        $this->assertCount(5, $list->getTasks());
        $this->assertCount(5, $list->getTodo());
        $this->assertCount(0, $list->getDone());
        $this->assertCount(1, $list->getProjects());
        $this->assertCount(1, $list->getContexts());
        $this->assertCount(1, $list->getMetadata());
    }
}
