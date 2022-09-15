<?php

use Lazer\Migrate\Classes\Database;
use Lazer\Classes\Database as LazerDatabase;
// use Lazer\Migrate\Test\VfsHelper\Config as TestHelper;

use org\bovigo\vfs\vfsStream;
// use org\bovigo\vfs\vfsStreamDirectory;

define('ROOT', realpath(__DIR__).'/../'); //Path to folder with tables
define('LAZER_DATA_PATH', 'vfs://data/'); //Path to folder with tables

describe("Database", function() {
    beforeAll(function() {
        $this->root = vfsStream::setup('data');
        vfsStream::copyFromFileSystem(ROOT . 'db');
        $this->object = new Database;
        $this->lazerObject = new LazerDatabase;
        $this->table = null;
    });

    function usersDataProvider(): array
    {
        $json = json_decode(file_get_contents(ROOT . 'db/users.json'), true);

        // $data = [
        //     'users',
        //     $json
        // ];

        return $json;
    }


    describe("::match()", function() {

        it("Check database", function() {
            expect(true)->toBe($this->root->hasChild('users.config.json'));
        });
        it("Check data not exists", function() {
            expect(false)->toBe($this->root->hasChild('users.data.json'));
        });
        it("Check data exists", function() {
            $this->table = $this->object->addDataToIndex('users', usersDataProvider());
            expect(true)->toBe($this->root->hasChild('users.data.json'));
        });

        it("Check class instance type", function() {
            $table = LazerDatabase::table('users');
            expect($table)->toBeAnInstanceOf(LazerDatabase::class);
        });

        it("Count matching", function() {
            $table = LazerDatabase::table('users');
            $results = $table->findAll();
            expect($results)->toHaveLength(4);
        });

        it("Count matching", function() {
            $table = LazerDatabase::table('users');
            $result = $table->find(2);
            expect($result)->toBeAnInstanceOf(LazerDatabase::class);
            expect($result->email)->toBe('kriss@example.com');
        });
    });
});
