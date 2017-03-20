<?php

namespace TodoTxt\Tests;

use TodoTxt\MetaData;

class MetaDataTest extends \PHPUnit_Framework_TestCase
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
        $this->setExpectedException('TodoTxt\Exceptions\EmptyArrayException');
        $metadata = new MetaData(array());
    }

    public function testInvalidArray()
    {
        // Invalid array
        $this->setExpectedException('TodoTxt\Exceptions\InvalidArrayException');
        $metadata = new MetaData(array('full' => 'key:value'));
    }

    public function testValidId()
    {
        $metadata = new MetaData(array('full' => 'key:value', 'key' => 'key', 'value' => 'value'));
        $this->assertEquals($metadata->getId(), md5(utf8_encode('key:value')));
    }
}
