<?php

namespace TodoTxt\Tests;

use TodoTxt\Task;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test simple tasks, whitespace trimming and a few edge cases
     */
    public function testStandard()
    {
        $task = new Task("Task with a @context");
        $this->assertEquals($task->contexts[0]->context, "context");
    }
    
    public function testEmpty()
    {
        $task = new Task("This is a task");
        $this->assertEmpty($task->contexts);
    }
}
