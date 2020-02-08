<?php declare(strict_types=1);

namespace Imjoehaines\Flowder\Truncator;

use PDO;

final class MySqlTruncator implements TruncatorInterface
{
    /**
     * @var PDO
     */
    private $db;

    /**
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Truncate the given table
     *
     * @param string $table
     * @return void
     */
    public function truncate(string $table): void
    {
        $this->db->exec('SET foreign_key_checks = 0');
        $this->db->exec('TRUNCATE TABLE `' . $table . '`');
        $this->db->exec('SET foreign_key_checks = 1');
    }
}
