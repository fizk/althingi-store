<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\TestCase;
use App\Service\Constituency;
use App\DatabaseConnectionTrait;

class ConstituencyTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreCreated()
    {
        //GIVE
        // ...

        //WHEN
        $result = (new Constituency())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'constituency_id' => 1,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
        ]);

        //THEN
        $actual = array_map(function(BSONDocument $item) {
            return $item->getArrayCopy();
        }, iterator_to_array($this->getDatabase()->selectCollection(Constituency::COLLECTION)->find([]), false));

        $expected = [[
            '_id' => 1,
            'constituency_id' => 1,
            'name' => 'name',
            'abbr_short' => 'abbr_short',
            'abbr_long' => 'abbr_long',
            'description' => 'description',
        ]];

        $createdResultCode = 1;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreUpdate()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Constituency::COLLECTION)->insertOne([
            '_id' => 1,
            'constituency_id' => 1,
            'name' => 'name',
            'abbr_short' => 'abbr_short',
            'abbr_long' => 'abbr_long',
            'description' => 'description',
        ]);

        //WHEN
        $result = (new Constituency())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'constituency_id' => 1,
                'name' => 'name',
                'abbr_short' => 'abbr_short-update',
                'abbr_long' => 'abbr_long-update',
                'description' => 'description-update',
        ]);

        //THEN
        $actual = array_map(function(BSONDocument $item) {
            return $item->getArrayCopy();
        }, iterator_to_array($this->getDatabase()->selectCollection(Constituency::COLLECTION)->find([]), false));

        $expected = [[
            '_id' => 1,
            'constituency_id' => 1,
            'name' => 'name',
            'abbr_short' => 'abbr_short-update',
            'abbr_long' => 'abbr_long-update',
            'description' => 'description-update',
        ]];

        $createdResultCode = 2;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreNoChange()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Constituency::COLLECTION)->insertOne([
            '_id' => 1,
            'constituency_id' => 1,
            'name' => 'name',
            'abbr_short' => 'abbr_short',
            'abbr_long' => 'abbr_long',
            'description' => 'description',
        ]);

        //WHEN
        $result = (new Constituency())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'constituency_id' => 1,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
        ]);

        //THEN
        $actual = array_map(function(BSONDocument $item) {
            return $item->getArrayCopy();
        }, iterator_to_array($this->getDatabase()->selectCollection(Constituency::COLLECTION)->find([]), false));

        $expected = [[
            '_id' => 1,
            'constituency_id' => 1,
            'name' => 'name',
            'abbr_short' => 'abbr_short',
            'abbr_long' => 'abbr_long',
            'description' => 'description',
        ]];

        $createdResultCode = 0;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testGet()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Constituency::COLLECTION)->insertOne([
            '_id' => 1,
            'constituency_id' => 1,
            'name' => 'name',
            'abbr_short' => 'abbr_short',
            'abbr_long' => 'abbr_long',
            'description' => 'description',
        ]);

        //WHEN
        $actual = (new Constituency())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        //THEN
        $expected = [
            '_id' => 1,
            'constituency_id' => 1,
            'name' => 'name',
            'abbr_short' => 'abbr_short',
            'abbr_long' => 'abbr_long',
            'description' => 'description',
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testGetNotFound()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Constituency::COLLECTION)->insertOne([
            '_id' => 1,
            'constituency_id' => 1,
            'name' => 'name',
            'abbr_short' => 'abbr_short',
            'abbr_long' => 'abbr_long',
            'description' => 'description',
        ]);

        //WHEN
        $actual = (new Constituency())
            ->setSourceDatabase($this->getDatabase())
            ->get(2);

        //THEN
        $expected = null;

        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Constituency::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'constituency_id' => 1,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
            ], [
                '_id' => 2,
                'constituency_id' => 2,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
            ]
        ]);

        //WHEN
        $actual = (new Constituency())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $expected = [
            [
                '_id' => 1,
                'constituency_id' => 1,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
            ], [
                '_id' => 2,
                'constituency_id' => 2,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
            ]
        ];

        $this->assertEquals($expected, $actual);
    }
}
