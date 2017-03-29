<?php
declare(strict_types=1);

namespace TodoTxt\Tests;

use TodoTxt\Project;
use TodoTxt\Exceptions;
use PHPUnit\Framework\TestCase;


class ProjectTest extends TestCase
{
    /**
     * Test class instantiation
     */
    public function testInstantiation()
    {
        $project = new Project();

        $this->assertInstanceOf("TodoTxt\Project", $project);
        $this->assertEmpty($project->getName());
        $this->assertEmpty($project->getId());
    }

    /**
     * Test simple instantiation with parameter
     */
    public function testStandard()
    {
        $project = new Project('test');

        $this->assertEquals('test', $project->getName());
        $this->assertEquals(md5(utf8_encode('test')), $project->getId());
    }

    /**
     * Test instantiation with static method
     */
    public function testStatic()
    {
        $project = Project::withString('test');

        $this->assertEquals("test", $project->getName());
    }

    /**
     * Test instantiation with empty string
     */
    public function testEmpty()
    {
        // Empty string
        $this->expectException(Exceptions\EmptyStringException::class);

        $project = new Project('');
    }

    /**
     * Test setting $name of empty object
     */
    public function testSettingName()
    {
        $project = new Project();
        $project->setName('test');

        $this->assertEquals('test', $project->getName());
        $this->assertEquals(md5(utf8_encode('test')), $project->getId());
    }
}
