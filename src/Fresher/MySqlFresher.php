<?php

declare(strict_types=1);

namespace Imjoehaines\Flowder\Fresher;

use PDO;

final class MySqlFresher implements FresherInterface
{
    /**
     * @var PDO
     */
    private $db;

    /**
     * @var boolean
     */
    private $isAvailable = false;

    /**
     * A cache of table freshness.
     *
     * @var array
     */
    private $tableFreshnessCache = [];

    /**
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;

        // UPDATE_TIME is not available unless innodb's file_per_table is ON
        $sth = $this->db->prepare(
            'SHOW GLOBAL VARIABLES LIKE \'innodb_file_per_table\''
        );
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        if (isset($result['Value']) && strcasecmp($result['Value'], 'ON')) {
            // we can handle freshness checks
            $this->isAvailable = true;
        }
    }

    /**
     * Does this table need freshening from the fixtures?
     *
     * @param string $table
     * @return boolean
     */
    public function needsFreshening($table): boolean
    {
        if (!$this->isAvailable) {
            // mysql freshness check unavailable; always freshen
            return true;
        }

        // @todo implement this method
        return true;
    }

    /**
     * Mark a table as fresh (fetch its update time from info_schema)
     *
     * @param string $table
     * @return void
     */
    public function markAsFresh($table): void
    {
        if (!$this->isAvailable) {
            return;
        }

        $sth = $this->db->prepare(
            'SELECT TABLE_NAME, UPDATE_TIME
               FROM INFORMATION_SCHEMA
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = :table'
        );
        $sth->execute(['table' => $table]);
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        if (!isset($result) || ($result === false) || !isset($result['UPDATE_TIME'])) {
            // could not get update time
            return;
        }
        $this->tableFreshnessCache[$table] = $result['UPDATE_TIME'];
    }
}
