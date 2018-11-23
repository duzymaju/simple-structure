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

    /**
     * Constructor
     *
     * @param array    $items items
     * @param int      $page  page
     * @param int|null $pack  pack
     */
    public function __construct(array $items = [], $page = 1, $pack = null)
    {
        parent::__construct($items);

        $this->page = max(1, (int) $page);
        $this->pack = isset($pack) ? max(1, (int) $pack) : null;
    }

    /**
     * Is last
     *
     * @return bool
     */
    public function isLast()
    {
        return !isset($pack) || $this->count() < $this->pack;
    }
}
