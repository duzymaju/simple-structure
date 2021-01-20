<?php

use PHPUnit\Framework\TestCase;
use SimpleStructure\Tool\PagesHelper;
use SimpleStructure\Tool\Paginator;

final class PagesHelperTest extends TestCase
{
    /**
     * Test invalid parameters
     */
    public function testInvalidParameters()
    {
        $pagesHelper = new PagesHelper(-23, 45.7);
        $this->assertEquals(1, $pagesHelper->page);
        $this->assertEquals(45, $pagesHelper->pack);
        $this->assertEquals(0, $pagesHelper->offset);
        $this->assertEquals(45, $pagesHelper->limit);
    }

    /**
     * Test empty pages helper
     */
    public function testEmptyPagesHelper()
    {
        $pagesHelper = new PagesHelper();
        $this->assertEquals(1, $pagesHelper->page);
        $this->assertEquals(null, $pagesHelper->pack);
        $this->assertEquals(0, $pagesHelper->offset);
        $this->assertEquals(null, $pagesHelper->limit);
        $paginator = $pagesHelper->getPaginator();
        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertEquals(1, $paginator->page);
        $this->assertEquals(null, $paginator->pack);
        $this->assertEquals(null, $paginator->pages);
    }

    /**
     * Test empty paginator
     */
    public function testEmptyPaginator()
    {
        $pagesHelper = new PagesHelper(12, 20);
        $this->assertEquals(12, $pagesHelper->page);
        $this->assertEquals(20, $pagesHelper->pack);
        $this->assertEquals(220, $pagesHelper->offset);
        $this->assertEquals(20, $pagesHelper->limit);
        $paginator = $pagesHelper->getPaginator();
        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertEquals(12, $paginator->page);
        $this->assertEquals(20, $paginator->pack);
        $this->assertEquals(null, $paginator->pages);
    }

    /**
     * Test custom page of paginator with total number
     */
    public function testCustomPageOfPaginatorWithTotalNumber()
    {
        $pagesHelper = new PagesHelper(3, 5);
        $this->assertEquals(3, $pagesHelper->page);
        $this->assertEquals(5, $pagesHelper->pack);
        $this->assertEquals(10, $pagesHelper->offset);
        $this->assertEquals(5, $pagesHelper->limit);
        $paginator = $pagesHelper->getPaginator([1, 2, 3, 4, 5], 300);
        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertEquals(3, $paginator->page);
        $this->assertEquals(5, $paginator->pack);
        $this->assertEquals(60, $paginator->pages);
    }
}
