<?php

namespace SimpleStructure\Tool;

/**
 * Pages helper tool
 */
class PagesHelper
{
    /** @var int */
    public $page;

    /** @var int|null */
    public $pack;

    /** @var int */
    public $offset;

    /** @var int|null */
    public $limit;

    /**
     * Construct
     *
     * @param int      $page page
     * @param int|null $pack pack
     */
    public function __construct($page = 1, $pack = null)
    {
        $this->page = max(1, (int) $page);
        if (isset($pack)) {
            $this->limit = max(1, (int) $pack);
            $this->offset = $this->limit * ($this->page - 1);
        } else {
            $this->limit = null;
            $this->offset = 0;
        }
        $this->pack = $this->limit;
    }

    /**
     * Get paginator
     *
     * @param array    $items       items
     * @param int|null $totalNumber total number
     *
     * @return Paginator
     */
    public function getPaginator(array $items = [], $totalNumber = null)
    {
        return new Paginator($items, $this->page, $this->pack, $totalNumber);
    }
}
