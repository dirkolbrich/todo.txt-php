<?php
declare(strict_types=1);

namespace TodoTxt\Tests;

use TodoTxt\Collection;
use PHPUnit\Framework\TestCase;


class CollectionTest extends TestCase
{
    /**
     * Test Collection instantiation
     */
    public function testInstantiation()
    {
        $list = new Collection();

        $this->assertInstanceOf("TodoTxt\Collection", $list);
        $this->assertEquals(0, $list->count());
        $this->assertEmpty($list->get());
    }

    /**
     * Test Collection instantiation with array
     */
    public function testInstantiationWithArray()
    {
        $array = array('one', 'two', 'three');
        $list = new Collection($array);

        $this->assertInstanceOf("TodoTxt\Collection", $list);
        $this->assertEquals(3, $list->count());
        $this->assertNotEmpty($list->get());
    }

    /**
     * Test Collection instantiation with static
     */
    public function testInstantiationStatic()
    {
        $list = Collection::make();

        $this->assertInstanceOf("TodoTxt\Collection", $list);
        $this->assertEquals(0, $list->count());
        $this->assertEmpty($list->get());
    }

    /**
     * Test static Collection instantiation with array
     */
    public function testStaticInstantiationWithArray()
    {
        $array = array('one', 'two', 'three');
        $list = Collection::make($array);

        $this->assertInstanceOf("TodoTxt\Collection", $list);
        $this->assertEquals(3, $list->count());
        $this->assertNotEmpty($list->get());
    }

    /**
     * Test adding item to Collection
     */
    public function testAdd()
    {
        $list = new Collection();
        $list->add('item1');

        $this->assertEquals(1, $list->count());
        $this->assertNotEmpty($list->get());
    }

    /**
     * Test deleting item from Collection
     */
    public function testDelete()
    {
        $list = new Collection();
        $list->add('item1');
        $list->delete(0);

        $this->assertEquals(0, $list->count());
        $this->assertEmpty($list->get());
    }

    /**
     * Test deleting item from Collection with reorder of index
     */
    public function testDeleteIndex()
    {
        $array = array('one', 'two', 'three');
        $list = new Collection($array);
        $list->delete(1);

        $this->assertEquals(2, $list->count());
        $this->assertArrayHasKey(1, $list->get());
        $this->assertArrayNotHasKey(2, $list->get());
    }

    /**
     * Test deleting not existing item from Collection
     */
    public function testInvalidDelete()
    {
        $list = new Collection();
        $list->add('item1');
        $list->delete(1);

        $this->assertEquals(1, $list->count());
        $this->assertNotEmpty($list->get());
    }

    /**
     * Test map function
     */
    public function testMap()
    {
        $array = array('one', 'two', 'three');
        $list = new Collection($array);

        $list = $list->map(function ($item) {
            return $item . '-test';
        });

        $this->assertEquals(3, count($list));
        $this->assertEquals('one-test', $list->first());
    }

    /**
     * Test filter function
     */
    public function testFilter()
    {
        $array = array('one', 'two', 'three');
        $list = new Collection($array);

        $list = $list->filter(function ($item) {
            return $item == 'two';
        });

        $this->assertEquals(1, count($list));
        $this->assertEquals('two', $list[0]);
    }

    /**
     * Test reject function
     */
    public function testReject()
    {
        $array = array('one', 'two', 'three');
        $list = new Collection($array);

        $list = $list->reject(function ($item) {
            return $item == 'two';
        });

        $this->assertEquals(2, count($list));
        $this->assertEquals('one', $list[0]);
        $this->assertEquals('three', $list[1]);
    }

    /**
     * Test reversing function
     */
    public function testReverse()
    {
        $array = array('one', 'two', 'three');
        $list = new Collection($array);
        $list = $list->reverse();

        $this->assertEquals(3, count($list));
        $this->assertEquals('three', $list->first());
        $this->assertEquals('one', $list->last());
    }

    /**
     * Test get first item from Collection
     */
    public function testFirst()
    {
        $array = array('one', 'two', 'three');
        $list = new Collection($array);

        $this->assertEquals('one', $list->first());
    }

    /**
     * Test get last item from Collection
     */
    public function testLast()
    {
        $array = array('one', 'two', 'three');
        $list = new Collection($array);

        $this->assertEquals('three', $list->last());
    }
}
