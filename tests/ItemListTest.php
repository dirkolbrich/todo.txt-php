<?php
declare(strict_types=1);

namespace TodoTxt\Tests;

use TodoTxt\ItemList;
use PHPUnit\Framework\TestCase;


class ItemListTest extends TestCase
{
    /**
     * Test ItemList instantiation
     */
    public function testInstantiation()
    {
        $list = new ItemList();
        $this->assertInstanceOf("TodoTxt\ItemList", $list);
        $this->assertEquals(0, $list->count());
        $this->assertEmpty($list->list);
    }

    /**
     * Test adding item to ItemList
     */
    public function testAdd()
    {
        $list = new ItemList();
        $list->add('item1');
        $this->assertEquals(1, $list->count());
        $this->assertNotEmpty($list->list);
    }

    /**
     * Test deleting item from ItemList
     */
    public function testDelete()
    {
        $list = new ItemList();
        $list->add('item1');
        $list->delete(0);
        $this->assertEquals(0, $list->count());
        $this->assertEmpty($list->list);
    }

    /**
     * Test deleting item from ItemList with reorder of index
     */
    public function testDeleteIndex()
    {
        $list = new ItemList();
        $list->add('item1');
        $list->add('item2');
        $list->add('item3');
        $list->delete(1);
        $this->assertEquals(2, $list->count());
        $this->assertArrayHasKey(1, $list->list);
        $this->assertArrayNotHasKey(2, $list->list);
    }


    /**
     * Test deleting not existing item from ItemList
     */
    public function testInvalidDelete()
    {
        $list = new ItemList();
        $list->add('item1');
        $list->delete(1);
        $this->assertEquals(1, $list->count());
        $this->assertNotEmpty($list->list);
    }

    /**
     * Test get first item from ItemList
     */
    public function testFirst()
    {
        $list = new ItemList();
        $list->add('item1');
        $list->add('item2');
        $list->add('item3');
        $this->assertEquals('item1', $list->first());
    }

    /**
     * Test get last item from ItemList
     */
    public function testLast()
    {
        $list = new ItemList();
        $list->add('item1');
        $list->add('item2');
        $list->add('item3');
        $this->assertEquals('item3', $list->last());
    }
}
