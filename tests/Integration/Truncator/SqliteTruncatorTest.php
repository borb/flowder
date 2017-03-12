<?php

namespace Imjoehaines\Flowder\Test\Integration\Truncator;

use PDO;
use PHPUnit\Framework\TestCase;
use Imjoehaines\Flowder\Truncator\SqliteTruncator;

class SqliteTruncatorTest extends TestCase
{
    public function testItTruncatesAGivenTable()
    {
        $db = new PDO('sqlite::memory:');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $db->exec('CREATE TABLE IF NOT EXISTS test_truncate_table (
            column1 INT PRIMARY KEY
        )');

        $statement = $db->prepare('INSERT INTO test_truncate_table VALUES (1), (2), (3)');
        $statement->execute();

        $statement = $db->prepare('SELECT * FROM test_truncate_table');
        $statement->execute();
        $actualBefore = $statement->fetchAll(PDO::FETCH_ASSOC);

        $truncator = new SqliteTruncator($db);
        $truncator->truncate('test_truncate_table');

        $actualAfter = $statement->fetchAll(PDO::FETCH_ASSOC);

        $expectedBefore = [['column1' => '1'], ['column1' => '2'], ['column1' => '3']];
        $expectedAfter = [];

        $this->assertSame($expectedBefore, $actualBefore);
        $this->assertSame($expectedAfter, $actualAfter);
    }

    public function testItResetsTheAutoIncrementValueOfTheTable()
    {
        $db = new PDO('sqlite::memory:');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $db->exec('CREATE TABLE IF NOT EXISTS test_truncate_table (
            column1 INTEGER PRIMARY KEY AUTOINCREMENT,
            column2 TEXT
        )');

        $statement = $db->prepare('INSERT INTO test_truncate_table VALUES (null, "a"), (null, "b"), (null, "c")');
        $statement->execute();

        $statement = $db->prepare('SELECT * FROM test_truncate_table');
        $statement->execute();
        $actualBefore = $statement->fetchAll(PDO::FETCH_ASSOC);

        $truncator = new SqliteTruncator($db);
        $truncator->truncate('test_truncate_table');

        $expectedBefore = [
            ['column1' => '1', 'column2' => 'a'],
            ['column1' => '2', 'column2' => 'b'],
            ['column1' => '3', 'column2' => 'c'],
        ];

        $expectedAfter = [['column1' => '1', 'column2' => 'z']];

        $statement = $db->prepare('INSERT INTO test_truncate_table VALUES (null, "z")');
        $statement->execute();

        $statement = $db->prepare('SELECT * FROM test_truncate_table');
        $statement->execute();
        $actualAfter = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->assertSame($expectedBefore, $actualBefore);
        $this->assertSame($expectedAfter, $actualAfter);
    }

    public function testItDoesntBreakWhenThereAreForeignKeys()
    {
        $db = new PDO('sqlite::memory:');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $db->exec('PRAGMA foreign_keys = ON');

        $db->exec('CREATE TABLE table_1 (
          id INTEGER PRIMARY KEY
        )');

        $db->exec('CREATE TABLE table_2 (
          id INTEGER PRIMARY KEY,
          table_1_id INTEGER NOT NULL,
          FOREIGN KEY(table_1_id) REFERENCES table_1(id)
        )');

        $statement = $db->prepare('INSERT INTO table_1 VALUES (1)');
        $statement->execute();

        $statement = $db->prepare('INSERT INTO table_2 VALUES (1, 1)');
        $statement->execute();

        $statement = $db->prepare('SELECT * FROM table_2');
        $statement->execute();
        $actualBefore = $statement->fetchAll(PDO::FETCH_ASSOC);

        $truncator = new SqliteTruncator($db);
        // truncate table 1 first to check foreign keys don't cause issues
        $truncator->truncate('table_1');
        $truncator->truncate('table_2');

        $actualAfter = $statement->fetchAll(PDO::FETCH_ASSOC);

        $expectedBefore = [['id' => '1', 'table_1_id' => '1']];
        $expectedAfter = [];

        $this->assertSame($expectedBefore, $actualBefore);
        $this->assertSame($expectedAfter, $actualAfter);
    }
}
