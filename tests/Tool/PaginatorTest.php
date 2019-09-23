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
        $this->assertEquals($paginator->count(), 0);
        $this->assertEquals($paginator->pack, null);
        $this->assertEquals($paginator->page, 1);
        $this->assertEquals($paginator->pages, null);
        $this->assertTrue($paginator->isLast());
    }

    /**
     * Test custom page of paginator without total number
     */
    public function testCustomPageOfPaginatorWithoutTotalNumber()
    {
        $paginator = new Paginator([ 6, 7, 8, 9, 10 ], 2, 5);
        $this->assertEquals($paginator->count(), 5);
        $this->assertEquals($paginator->pack, 5);
        $this->assertEquals($paginator->page, 2);
        $this->assertEquals($paginator->pages, null);
        $this->assertFalse($paginator->isLast());
    }

    /**
     * Test apparent last page of paginator without total number
     */
    public function testApparentLastPageOfPaginatorWithoutTotalNumber()
    {
        $paginator = new Paginator([ 11, 12, 13, 14, 15 ], 3, 5);
        $this->assertEquals($paginator->count(), 5);
        $this->assertEquals($paginator->pack, 5);
        $this->assertEquals($paginator->page, 3);
        $this->assertEquals($paginator->pages, null);
        $this->assertFalse($paginator->isLast());
    }

    /**
     * Test real last page of paginator without total number
     */
    public function testRelLastPageOfPaginatorWithoutTotalNumber()
    {
        $paginator = new Paginator([ 11, 12, 13 ], 3, 5);
        $this->assertEquals($paginator->count(), 3);
        $this->assertEquals($paginator->pack, 5);
        $this->assertEquals($paginator->page, 3);
        $this->assertEquals($paginator->pages, null);
        $this->assertTrue($paginator->isLast());
    }

    /**
     * Test custom page of paginator with total number
     */
    public function testCustomPageOfPaginatorWithTotalNumber()
    {
        $paginator = new Paginator([ 6, 7, 8, 9, 10 ], 2, 5, 13);
        $this->assertEquals($paginator->count(), 5);
        $this->assertEquals($paginator->pack, 5);
        $this->assertEquals($paginator->page, 2);
        $this->assertEquals($paginator->pages, 3);
        $this->assertFalse($paginator->isLast());
    }

    /**
     * Test apparent last page of paginator with total number
     */
    public function testApparentLastPageOfPaginatorWithTotalNumber()
    {
        $paginator = new Paginator([ 11, 12, 13, 14, 15 ], 3, 5, 15);
        $this->assertEquals($paginator->count(), 5);
        $this->assertEquals($paginator->pack, 5);
        $this->assertEquals($paginator->page, 3);
        $this->assertEquals($paginator->pages, 3);
        $this->assertTrue($paginator->isLast());
    }

    /**
     * Test real last page of paginator with total number
     */
    public function testRealLastPageOfPaginatorWithTotalNumber()
    {
        $paginator = new Paginator([ 11, 12, 13 ], 3, 5, 13);
        $this->assertEquals($paginator->count(), 3);
        $this->assertEquals($paginator->pack, 5);
        $this->assertEquals($paginator->page, 3);
        $this->assertEquals($paginator->pages, 3);
        $this->assertTrue($paginator->isLast());
    }
}
