<?php

namespace TodoTxt\Tests;

use TodoTxt\Task;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test simple tasks, whitespace trimming and a few edge cases
     */
    public function testStandard()
    {
        $task = new Task("Task with a +project");
        $this->assertEquals($task->projects[0]->project, "project");
    }
    
    public function testEmpty()
    {
        $task = new Task("This is a task");
        $this->assertEmpty($task->projects);
    }
}
