<?php
declare(strict_types=1);

namespace TodoTxt\Tests;

use TodoTxt\Context;
use TodoTxt\Exceptions;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    /**
     * Test class instantiation
     */
    public function testInstantiation()
    {
        $context = new Context();

        $this->assertInstanceOf("TodoTxt\Context", $context);
        $this->assertEmpty($context->getName());
    }

    /**
     * Test simple instantiation with parameter
     */
    public function testStandard()
    {
        $context = new Context('test');

        $this->assertEquals('test', $context->getName());
    }

    /**
     * Test instantiation with static method
     */
    public function testStatic()
    {
        $context = Context::withString('test');

        $this->assertEquals("test", $context->getName());
    }

    public function testEmpty()
    {
        // Empty string
        $this->expectException(Exceptions\EmptyStringException::class);
        $context = new Context('');
    }

    /**
     * Test setting $name of empty object
     */
    public function testSettingName()
    {
        $context = new Context();
        $context->setName('test');

        $this->assertEquals('test', $context->getName());
    }

    /**
     * Test setting $name of empty object
     */
    public function testSettingNameWithEmptyString()
    {
        // Empty string
        $this->expectException(Exceptions\EmptyStringException::class);

        $context = new Context('test');
        $context->setName('');
    }
}
