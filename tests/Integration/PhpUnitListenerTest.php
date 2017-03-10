<?php

namespace Imjoehaines\Flowder\Test\Integration;

use PDO;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Imjoehaines\Flowder\PhpUnitListener;
use Imjoehaines\Flowder\Loader\FileLoader;
use Imjoehaines\Flowder\Loader\DirectoryLoader;
use Imjoehaines\Flowder\Persister\PdoPersister;

class PhpUnitListenerTest extends TestCase
{
    public function testItThrowsIfGivenPathDoesntExist()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The file or directory "not a valid path" does not exist!');

        $db = new PDO('sqlite::memory:');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $persister = new PdoPersister($db);
        $loader = new FileLoader($persister);

        $listener = new PhpUnitListener('not a valid path', $loader);
    }

    public function testItLoadsFixturesFromAFileIfGivenThePathToAFile()
    {
        $this->markTestSkipped('');
        $db = new PDO('sqlite::memory:');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $db->exec('CREATE TABLE IF NOT EXISTS loader_test_data (
            column1 INT PRIMARY KEY,
            column2 INT,
            column3 TEXT
        )');

        $persister = new PdoPersister($db);
        $loader = new FileLoader($persister);

        $listener = new PhpUnitListener(__DIR__ . '/../data/loader_test_data.php', $loader);

        $listener->startTest($this);

        $statement = $db->prepare('SELECT * FROM loader_test_data');
        $statement->execute();
        $actual = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->assertSame(
            [
                [
                    'column1' => '1',
                    'column2' => '2',
                    'column3' => 'three',
                ],
                [
                    'column1' => '4',
                    'column2' => '5',
                    'column3' => 'six',
                ],
                [
                    'column1' => '7',
                    'column2' => '8',
                    'column3' => 'nine',
                ],
            ],
            $actual
        );
    }

    public function testItLoadsFixturesFromADirectoryIfGivenThePathToADirectory()
    {
        $this->markTestSkipped('');
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

        $listener = new PhpUnitListener(__DIR__ . '/../data/directory_loader_test', $loader);

        $listener->startTest($this);

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
