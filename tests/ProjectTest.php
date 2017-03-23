<?php
declare(strict_types=1);

namespace TodoTxt\Tests;

use TodoTxt\Project;
use TodoTxt\Exceptions;
use PHPUnit\Framework\TestCase;


class ProjectTest extends TestCase
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
        $this->expectException(Exceptions\EmptyStringException::class);
        $project = new Project("");
    }
}
