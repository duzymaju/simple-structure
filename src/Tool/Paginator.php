<?php

namespace SimpleStructure\Tool;

/**
 * Paginator tool
 */
class Paginator extends ArrayObject
{
    /** @var int */
    public $page;

    /** @var int|null */
    public $pack;

    /** @var int|null */
    public $pages;

    /**
     * Constructor
     *
     * @param array    $items       items
     * @param int      $page        page
     * @param int|null $pack        pack
     * @param int|null $totalNumber total number
     */
    public function __construct(array $items = [], $page = 1, $pack = null, $totalNumber = null)
    {
        parent::__construct($items);

        $this->page = max(1, (int) $page);
        $this->pack = isset($pack) ? max(1, (int) $pack) : null;
        $this->pages = !isset($totalNumber) ? null :
            (isset($this->pack) ? ceil(max(0, (int) $totalNumber) / $this->pack) : 1);
    }

    /**
     * Is last
     *
     * @return bool
     */
    public function isLast()
    {
        if (!isset($this->pack)) {
            return true;
        }
        $isLast = isset($this->pages) ? $this->page >= $this->pages : $this->pack > $this->count();

        return $isLast;
    }
}
