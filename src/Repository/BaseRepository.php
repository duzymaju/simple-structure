<?php

namespace SimpleStructure\Repository;

use PDO;
use SimpleStructure\Tool\Paginator;

/**
 * Base repository
 */
abstract class BaseRepository
{
    /** @var PDO */
    protected $db;

    /**
     * Construct
     *
     * @param PDO $db database connection
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get paginated
     *
     * @param string $statement statement
     * @param array  $params    params
     * @param int    $page      page
     * @param int    $pack      pack
     *
     * @return Paginator
     */
    public function getPaginated($statement, array $params, $page = 1, $pack = null)
    {
        $page = max(1, (int) $page);
        $pack = isset($pack) ? max(1, (int) $pack) : null;

        if (isset($pack)) {
            $limit = $pack;
            $offset = $pack * ($page - 1);
            $statement .= ' LIMIT ' . $offset . ', ' . $limit;
        }

        $query = $this->db->prepare($statement);
        $query->execute($params);

        $items = [];
        while ($item = $query->fetch()) {
            $items[] = $item;
        }
        $paginator = new Paginator($items, $page, $pack);

        return $paginator;
    }
}
