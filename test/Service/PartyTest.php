<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\TestCase;
use App\Service\Party;
use App\DatabaseConnectionTrait;

class PartyTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreCreate()
    {
        //GIVEN
        $expected = [ [
            '_id' => 1,
            'party_id' => 1,
            'name' => 'name-1',
            'abbr_short' => null,
            'abbr_long' => null,
            'color' => null,
        ]];
        $createdResultCode = 1;

        //WHEN
        $result = (new Party())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'party_id' => 1,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
        ]);

        //THEN
        $actual = array_map(function(BSONDocument $item) {
            return $item->getArrayCopy();
        }, iterator_to_array($this->getDatabase()->selectCollection(Party::COLLECTION)->find([]), false));

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreNoAction()
    {
        //GIVEN
        $expected = [ [
            '_id' => 1,
            'party_id' => 1,
            'name' => 'name-1',
            'abbr_short' => null,
            'abbr_long' => null,
            'color' => null,
        ]];
        $noActionResultCode = 0;

        $this->getDatabase()->selectCollection(Party::COLLECTION)->insertOne([
            '_id' => 1,
            'party_id' => 1,
            'name' => 'name-1',
            'abbr_short' => null,
            'abbr_long' => null,
            'color' => null,
        ]);

        //WHEN
        $result = (new Party())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'party_id' => 1,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
        ]);

        //THEN
        $actual = array_map(function(BSONDocument $item) {
            return $item->getArrayCopy();
        }, iterator_to_array($this->getDatabase()->selectCollection(Party::COLLECTION)->find([]), false));

        $this->assertEquals($expected, $actual);
        $this->assertEquals($noActionResultCode, $result);
    }

    public function testGetNotFound()
    {
        //GIVE
        $expected = null;

        $this->getDatabase()->selectCollection(Party::COLLECTION)->insertOne([
            '_id' => 1,
            'party_id' => 1,
            'name' => 'name-1',
            'abbr_short' => null,
            'abbr_long' => null,
            'color' => null,
        ]);

        //WHEN
        $actual = (new Party())
            ->setSourceDatabase($this->getDatabase())
            ->get(2);

        //THEN
        $this->assertEquals($expected, $actual);
    }

    public function testGet()
    {
        //GIVE
        $expected = [
            '_id' => 1,
            'party_id' => 1,
            'name' => 'name-1',
            'abbr_short' => null,
            'abbr_long' => null,
            'color' => null,
        ];

        $this->getDatabase()->selectCollection(Party::COLLECTION)->insertOne([
            '_id' => 1,
            'party_id' => 1,
            'name' => 'name-1',
            'abbr_short' => null,
            'abbr_long' => null,
            'color' => null,
        ]);

        //WHEN
        $actual = (new Party())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        //THEN
        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $expected = [[
            '_id' => 1,
            'party_id' => 1,
            'name' => 'name-1',
            'abbr_short' => null,
            'abbr_long' => null,
            'color' => null,
        ], [
            '_id' => 2,
            'party_id' => 2,
            'name' => 'name-1',
            'abbr_short' => null,
            'abbr_long' => null,
            'color' => null,
        ]];

        $this->getDatabase()->selectCollection(Party::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'party_id' => 1,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
            ],
            [
                '_id' => 2,
                'party_id' => 2,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
            ],
        ]);

        //WHEN
        $actual = (new Party())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $this->assertEquals($expected, $actual);
    }
}
