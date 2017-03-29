<?php
declare(strict_types=1);

namespace TodoTxt\Tests;

use TodoTxt\MetaData;
use TodoTxt\Exceptions;
use PHPUnit\Framework\TestCase;


class MetaDataTest extends TestCase
{
    /**
     * Test class instantiation
     */
    public function testInstantiation()
    {
        $metadata = new MetaData();

        $this->assertInstanceOf("TodoTxt\MetaData", $metadata);
        $this->assertEmpty($metadata->getKey());
        $this->assertEmpty($metadata->getValue());
        $this->assertEmpty($metadata->getId());
    }

    /**
     * Test simple metadata
     */
    public function testStandard()
    {
        $metadata = new MetaData(array('full' => 'key:value', 'key' => 'key', 'value' => 'value'));

        $this->assertEquals('key', $metadata->getKey());
        $this->assertEquals('value', $metadata->getValue());
        $this->assertEquals(md5(utf8_encode('key:value')), $metadata->getId());
    }

    /**
     * Test instantiation with static method
     */
    public function testStatic()
    {
        $array = array('full' => 'key:value', 'key' => 'key', 'value' => 'value');
        $metadata = MetaData::withArray($array);

        $this->assertInstanceOf("TodoTxt\MetaData", $metadata);
        $this->assertEquals('key', $metadata->getKey());
        $this->assertEquals('value', $metadata->getValue());
        $this->assertEquals(md5(utf8_encode('key:value')), $metadata->getId());
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

    public function testSetKey()
    {
        $metadata = new MetaData(array('full' => 'key:value', 'key' => 'key', 'value' => 'value'));
        $metadata->setKey('test');

        $this->assertEquals('test', $metadata->getKey());
        $this->assertEquals('value', $metadata->getValue());
        $this->assertEquals($metadata->getId(), md5(utf8_encode('test:value')));
    }

    public function testSetEmptyKey()
    {
        // Empty string
        $this->expectException(Exceptions\EmptyStringException::class);

        $metadata = new MetaData(array('full' => 'key:value', 'key' => 'key', 'value' => 'value'));
        $metadata->setKey('');
    }

    public function testSetValue()
    {
        $metadata = new MetaData(array('full' => 'key:value', 'key' => 'key', 'value' => 'value'));
        $metadata->setValue('test');

        $this->assertEquals('key', $metadata->getKey());
        $this->assertEquals('test', $metadata->getValue());
        $this->assertEquals($metadata->getId(), md5(utf8_encode('key:test')));
    }

    public function testSetEmptyValue()
    {
        // Empty string
        $this->expectException(Exceptions\EmptyStringException::class);

        $metadata = new MetaData(array('full' => 'key:value', 'key' => 'key', 'value' => 'value'));
        $metadata->setValue('');
    }
}
