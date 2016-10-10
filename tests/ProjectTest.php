<?php

namespace TodoTxt\Tests;

use TodoTxt\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test simple tasks, whitespace trimming and a few edge cases
     */
    public function testStandard()
    {
        $project = new Project("project");
        $this->assertEquals($project->project, "project");
    }
    
    public function testEmpty()
    {
        // Empty string
        $this->setExpectedException('TodoTxt\Exceptions\EmptyStringException');
        $project = new Project("");
    }
}
