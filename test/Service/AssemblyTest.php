<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\Assembly;
use App\DatabaseConnectionTrait;
use DateTime;

class AssemblyTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreSimpleStructureCreate()
    {
        //GIVEN
        $expected = [ [
            '_id' => 1,
            'assembly_id' => 1,
            'from' => null,
            'to' => null,
        ]];
        $createdResultCode = 1;

        //WHEN
        $result = (new Assembly())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'assembly_id' => 1,
                'from' => null,
                'to' => null,
        ]);

        //THEN
        $actual = array_map(function(BSONDocument $item) {
            return $item->getArrayCopy();
        }, iterator_to_array($this->getDatabase()->selectCollection(Assembly::COLLECTION)->find([]), false));

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreWithDateCreate()
    {
        //GIVEN
        $expected = [ [
            '_id' => 1,
            'assembly_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
        ]];
        $createdResultCode = 1;

        //WHEN
        $result = (new Assembly())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'assembly_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
        ]);

        //THEN
        $actual = array_map(function(BSONDocument $item) {
            return $item->getArrayCopy();
        }, iterator_to_array($this->getDatabase()->selectCollection(Assembly::COLLECTION)->find([]), false));

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreNoAction()
    {
        //GIVEN
        $expected = [ [
            '_id' => 1,
            'assembly_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
        ]];
        $noActionResultCode = 0;

        $this->getDatabase()->selectCollection(Assembly::COLLECTION)->insertOne([
            '_id' => 1,
            'assembly_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
        ]);

        //WHEN
        $result = (new Assembly())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'assembly_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
        ]);

        //THEN
        $actual = array_map(function(BSONDocument $item) {
            return $item->getArrayCopy();
        }, iterator_to_array($this->getDatabase()->selectCollection(Assembly::COLLECTION)->find([]), false));

        $this->assertEquals($expected, $actual);
        $this->assertEquals($noActionResultCode, $result);
    }

    public function testStoreWithDateUpdate()
    {
        //GIVEN
        $expected = [ [
            '_id' => 1,
            'assembly_id' => 1,
            'from' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
        ]];
        $updateResultCode = 2;

        $this->getDatabase()->selectCollection(Assembly::COLLECTION)->insertOne([
            '_id' => 1,
            'assembly_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
        ]);

        //WHEN
        $result = (new Assembly())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'assembly_id' => 1,
                'from' => '2002-01-01',
                'to' => '2002-01-01',
        ]);

        //THEN
        $actual = array_map(function(BSONDocument $item) {
            return $item->getArrayCopy();
        }, iterator_to_array($this->getDatabase()->selectCollection(Assembly::COLLECTION)->find([]), false));

        $this->assertEquals($expected, $actual);
        $this->assertEquals($updateResultCode, $result);
    }

    public function testGetNotFound()
    {
        //GIVE
        $expected = null;

        $this->getDatabase()->selectCollection(Assembly::COLLECTION)->insertOne([
            '_id' => 1,
            'assembly_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
        ]);

        //WHEN
        $actual = (new Assembly())
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
            'assembly_id' => 1,
            'from' => '2001-01-01T00:00:00+00:00',
            'to' => '2001-01-01T00:00:00+00:00',
        ];

        $this->getDatabase()->selectCollection(Assembly::COLLECTION)->insertOne([
            '_id' => 1,
            'assembly_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
        ]);

        //WHEN
        $actual = (new Assembly())
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
            'assembly_id' => 1,
            'from' => '2001-01-01T00:00:00+00:00',
            'to' => '2001-01-01T00:00:00+00:00',
        ], [
            '_id' => 2,
            'assembly_id' => 2,
            'from' => '2002-01-01T00:00:00+00:00',
            'to' => '2002-01-01T00:00:00+00:00',
        ]];

        $this->getDatabase()->selectCollection(Assembly::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'assembly_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ],
            [
                '_id' => 2,
                'assembly_id' => 2,
                'from' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
            ],
        ]);

        //WHEN
        $actual = (new Assembly())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $this->assertEquals($expected, $actual);
    }
}
