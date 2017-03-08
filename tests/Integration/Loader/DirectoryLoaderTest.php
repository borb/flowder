<?php

namespace Imjoehaines\Flowder\Test\Integration\Persister;

use PDO;
use PHPUnit\Framework\TestCase;
use Imjoehaines\Flowder\Loader\DirectoryLoader;
use Imjoehaines\Flowder\Persister\PdoPersister;

class DirectoryLoaderTest extends TestCase
{
    public function testItLoadsFixturesFromAGivenDirectory()
    {
        $db = new PDO('sqlite::memory:');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $db->exec('CREATE TABLE IF NOT EXISTS test_data_1 (
            column1 INT PRIMARY KEY,
            column2 INT,
            column3 TEXT
        )');

        $db->exec('CREATE TABLE IF NOT EXISTS test_data_2 (
            column4 INT PRIMARY KEY,
            column5 INT,
            column6 TEXT
        )');

        $db->exec('CREATE TABLE IF NOT EXISTS test_data_3 (
            column7 INT PRIMARY KEY,
            column8 INT,
            column9 TEXT
        )');

        $persister = new PdoPersister($db);

        $loader = new DirectoryLoader($persister);

        $loader->load(__DIR__ . '/../../data/directory_loader_test/');

        $statement = $db->prepare('SELECT * FROM test_data_1');
        $statement->execute();
        $actual = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->assertSame(
            [
                [
                    'column1' => '1',
                    'column2' => '2',
                    'column3' => 'three',
                ],
            ],
            $actual
        );

        $statement = $db->prepare('SELECT * FROM test_data_2');
        $statement->execute();
        $actual = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->assertSame(
            [
                [
                    'column4' => '4',
                    'column5' => '5',
                    'column6' => 'six',
                ],
            ],
            $actual
        );

        $statement = $db->prepare('SELECT * FROM test_data_3');
        $statement->execute();
        $actual = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->assertSame(
            [
                [
                    'column7' => '7',
                    'column8' => '8',
                    'column9' => 'nine',
                ],
            ],
            $actual
        );
    }

    public function testItLoadsFixturesFromAGivenDirectoryWithoutTrailingSlash()
    {
        $db = new PDO('sqlite::memory:');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $db->exec('CREATE TABLE IF NOT EXISTS test_data_1 (
            column1 INT PRIMARY KEY,
            column2 INT,
            column3 TEXT
        )');

        $db->exec('CREATE TABLE IF NOT EXISTS test_data_2 (
            column4 INT PRIMARY KEY,
            column5 INT,
            column6 TEXT
        )');

        $db->exec('CREATE TABLE IF NOT EXISTS test_data_3 (
            column7 INT PRIMARY KEY,
            column8 INT,
            column9 TEXT
        )');

        $persister = new PdoPersister($db);

        $loader = new DirectoryLoader($persister);

        $loader->load(__DIR__ . '/../../data/directory_loader_test');

        $statement = $db->prepare('SELECT * FROM test_data_1');
        $statement->execute();
        $actual = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->assertSame(
            [
                [
                    'column1' => '1',
                    'column2' => '2',
                    'column3' => 'three',
                ],
            ],
            $actual
        );

        $statement = $db->prepare('SELECT * FROM test_data_2');
        $statement->execute();
        $actual = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->assertSame(
            [
                [
                    'column4' => '4',
                    'column5' => '5',
                    'column6' => 'six',
                ],
            ],
            $actual
        );

        $statement = $db->prepare('SELECT * FROM test_data_3');
        $statement->execute();
        $actual = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->assertSame(
            [
                [
                    'column7' => '7',
                    'column8' => '8',
                    'column9' => 'nine',
                ],
            ],
            $actual
        );
    }
}
