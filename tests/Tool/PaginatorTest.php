<?php

use PHPUnit\Framework\TestCase;
use SimpleStructure\Tool\Paginator;

final class PaginatorTest extends TestCase
{
    /**
     * Test empty paginator
     */
    public function testEmptyPaginator()
    {
        $paginator = new Paginator();
        $this->assertEquals(0, $paginator->count());
        $this->assertEquals(null, $paginator->pack);
        $this->assertEquals(1, $paginator->page);
        $this->assertEquals(null, $paginator->pages);
        $this->assertTrue($paginator->isLast());
        $this->assertEquals('{"isLast":true,"list":[],"pack":null,"page":1}', json_encode($paginator));
    }

    /**
     * Test custom page of paginator without total number
     */
    public function testCustomPageOfPaginatorWithoutTotalNumber()
    {
        $paginator = new Paginator([6, 7, 8, 9, 10], 2, 5);
        $this->assertEquals(5, $paginator->count());
        $this->assertEquals(5, $paginator->pack);
        $this->assertEquals(2, $paginator->page);
        $this->assertEquals(null, $paginator->pages);
        $this->assertFalse($paginator->isLast());
    }

    /**
     * Test apparent last page of paginator without total number
     */
    public function testApparentLastPageOfPaginatorWithoutTotalNumber()
    {
        $paginator = new Paginator([11, 12, 13, 14, 15], 3, 5);
        $this->assertEquals(5, $paginator->count());
        $this->assertEquals(5, $paginator->pack);
        $this->assertEquals(3, $paginator->page);
        $this->assertEquals(null, $paginator->pages);
        $this->assertEquals([[11, 12], [13], [14], [15]], $paginator->getInGroups(4));
        $this->assertFalse($paginator->isLast());
    }

    /**
     * Test real last page of paginator without total number
     */
    public function testRelLastPageOfPaginatorWithoutTotalNumber()
    {
        $paginator = new Paginator([11, 12, 13], 3, 5);
        $this->assertEquals(3, $paginator->count());
        $this->assertEquals(5, $paginator->pack);
        $this->assertEquals(3, $paginator->page);
        $this->assertEquals(null, $paginator->pages);
        $this->assertEquals([[11, 12, 13]], $paginator->getInGroups(1));
        $this->assertTrue($paginator->isLast());
    }

    /**
     * Test custom page of paginator with total number
     */
    public function testCustomPageOfPaginatorWithTotalNumber()
    {
        $paginator = new Paginator([6, 7, 8, 9, 10], 2, 5, 13);
        $this->assertEquals(5, $paginator->count());
        $this->assertEquals(5, $paginator->pack);
        $this->assertEquals(2, $paginator->page);
        $this->assertEquals(3, $paginator->pages);
        $this->assertEquals([[6, 7, 8], [9, 10]], $paginator->getInGroups(2));
        $this->assertFalse($paginator->isLast());
    }

    /**
     * Test apparent last page of paginator with total number
     */
    public function testApparentLastPageOfPaginatorWithTotalNumber()
    {
        $paginator = new Paginator([11, 12, 13, 14, 15], 3, 5, 15);
        $this->assertEquals(5, $paginator->count());
        $this->assertEquals(5, $paginator->pack);
        $this->assertEquals(3, $paginator->page);
        $this->assertEquals(3, $paginator->pages);
        $this->assertEquals([[11, 12], [13, 14], [15]], $paginator->getInGroups(3));
        $this->assertTrue($paginator->isLast());
    }

    /**
     * Test real last page of paginator with total number
     */
    public function testRealLastPageOfPaginatorWithTotalNumber()
    {
        $paginator = new Paginator([11, 12], 3, 5, 12);
        $this->assertEquals(2, $paginator->count());
        $this->assertEquals(5, $paginator->pack);
        $this->assertEquals(3, $paginator->page);
        $this->assertEquals(3, $paginator->pages);
        $this->assertEquals([[11], [12]], $paginator->getInGroups(2));
        $this->assertTrue($paginator->isLast());
        $this->assertEquals('{"isLast":true,"list":[11,12],"pack":5,"page":3,"pages":3}', json_encode($paginator));
    }
}
