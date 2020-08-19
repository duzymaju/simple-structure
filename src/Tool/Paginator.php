<?php

namespace SimpleStructure\Tool;

use JsonSerializable;

/**
 * Paginator tool
 */
class Paginator extends ArrayObject implements JsonSerializable
{
    /** @var int */
    public $page;

    /** @var int|null */
    public $pack;

    /** @var int|null */
    public $pages;

    /** @var int|null */
    public $total;

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
            (isset($this->pack) ? (int) ceil(max(0, (int) $totalNumber) / $this->pack) : 1);
        $this->total = $totalNumber;
    }

    /**
     * Get in groups
     *
     * @param int $groupsNumber groups number
     *
     * @return array
     */
    public function getInGroups($groupsNumber)
    {
        $itemGroups = [];
        $offset = 0;
        for ($i = 0; $i < $groupsNumber; $i++) {
            $limit = ceil(($this->count() - $offset) / ($groupsNumber - $i));
            $itemGroups[] = array_slice($this->getArrayCopy(), $offset, $limit);
            $offset += $limit;
        }

        return $itemGroups;
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

    /**
     * JSON serialize
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $result = [
            'isLast' => $this->isLast(),
            'list' => $this->getArrayCopy(),
            'pack' => $this->pack,
            'page' => $this->page,
            'total' => $this->total,
        ];
        if (isset($this->pages)) {
            $result['pages'] = $this->pages;
        }

        return $result;
    }
}
