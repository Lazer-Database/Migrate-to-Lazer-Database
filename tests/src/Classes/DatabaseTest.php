<?php

declare(strict_types=1);

namespace Lazer\Migrate\Test\Classes;

use Lazer\Migrate\Classes\Database;
use Lazer\Classes\Database as LazerDatabase;
use Lazer\Migrate\Test\VfsHelper\Config as TestHelper;

class DatabaseTest extends \PHPUnit\Framework\TestCase
{

    use TestHelper;

    /**
     * @var Database
     */
    protected $object;

    protected $lazerObject;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->setUpFilesystem();
        $this->object = new Database;
        $this->lazerObject = new LazerDatabase;
    }

    public function usersDataProvider(): array
    {
        $json = json_decode(file_get_contents(ROOT . 'tests/db/users.json'), true);

        $data = [
            'users',
            $json
        ];

        return [$data];
    }

    /**
     * @testdox Converts array to structure and data of Lazer database: validated check table, fetchAll, fetch and validate data
     *
     * @covers \Lazer\Migrate\Test\Classes::create
     * @dataProvider usersDataProvider
     */
    public function testAddDataToIndex($table, $tableData)
    {
        $this->assertTrue($this->root->hasChild('users.config.json')); // check table
        $this->assertFalse($this->root->hasChild('users.data.json')); // here no data
        $table = $this->object->addDataToIndex($table, $tableData);
        $this->assertTrue($this->root->hasChild('users.data.json'), 'User table update');
        $this->assertTrue($this->root->hasChild('users.config.json'), 'User data added');

        /** @testdox Check table */
        $this->assertInstanceOf('Lazer\Classes\Database', $table);
        $results = $table->findAll();
        $this->assertInstanceOf('Lazer\Classes\Database', $results);
        $this->assertSame(4, count($results));

        $result = [];

        $result[] = $table->find();
        $this->assertInstanceOf('Lazer\Classes\Database', $result[0]);
        $this->assertSame(1, count($result[0]));

        $result[] = $table->find(2);
        $this->assertInstanceOf('Lazer\Classes\Database', $result[1]);
        $this->assertSame(1, count($result[1]));
        $this->assertSame(2, $result[1]->id);
        $this->assertSame('kriss@example.com', $result[1]->email);
    }
}
