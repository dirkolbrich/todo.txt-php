<?php
declare(strict_types=1);

namespace TodoTxt\Tests;

use TodoTxt\MetaData;
use TodoTxt\Exceptions;
use PHPUnit\Framework\TestCase;


class MetaDataTest extends TestCase
{
    /**
     * Test simple metadata
     */
    public function testStandard()
    {
        $metadata = new MetaData(array('full' => 'key:value', 'key' => 'key', 'value' => 'value'));
        $this->assertEquals($metadata->key, "key");
        $this->assertEquals($metadata->value, "value");
    }

    public function testEmptyArray()
    {
        // Empty array
        $this->expectException(Exceptions\EmptyArrayException::class);
        $metadata = new MetaData(array());
    }

    public function testInvalidArray()
    {
        // Invalid array
        $this->expectException(Exceptions\InvalidArrayException::class);
        $metadata = new MetaData(array('full' => 'key:value'));
    }

    public function testValidId()
    {
        $metadata = new MetaData(array('full' => 'key:value', 'key' => 'key', 'value' => 'value'));
        $this->assertEquals($metadata->getId(), md5(utf8_encode('key:value')));
    }
}
