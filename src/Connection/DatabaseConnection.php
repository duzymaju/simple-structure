<?php

namespace SimpleStructure\Connection;

use PDO;

/**
 * Database connection
 */
class DatabaseConnection extends PDO
{
    /**
     * Construct
     *
     * @param string $host     host
     * @param string $dbName   database name
     * @param string $user     user
     * @param string $password password
     * @param int    $port     port
     * @param string $charset  charset
     */
    public function __construct($host, $dbName, $user, $password, $port = 3306, $charset = 'utf8')
    {
        $dsnParts = [
            'dbname' => $dbName,
            'host' => $host,
            'port' => (int) $port,
        ];
        $dsn = 'mysql:' . implode(';', array_map(function ($key, $value) {
            return sprintf('%s=%s', $key, $value);
        }, array_keys($dsnParts), $dsnParts));

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => sprintf('SET NAMES %s', $charset),
        ];

        parent::__construct($dsn, $user, $password, $options);
    }
}
