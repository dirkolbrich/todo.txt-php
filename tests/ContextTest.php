<?php

namespace TodoTxt\Tests;

use TodoTxt\Context;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test simple tasks, whitespace trimming and a few edge cases
     */
    public function testStandard()
    {
        $context = new Context("context");
        $this->assertEquals($context->context, "context");
    }
    
    public function testEmpty()
    {
        // Empty string
        $this->setExpectedException('TodoTxt\Exceptions\EmptyStringException');
        $context = new Context("");
    }
}
