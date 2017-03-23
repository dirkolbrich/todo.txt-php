<?php
declare(strict_types=1);

namespace TodoTxt\Tests;

use TodoTxt\Context;
use TodoTxt\Exceptions;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
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
        $this->expectException(Exceptions\EmptyStringException::class);
        $context = new Context("");
    }
}
