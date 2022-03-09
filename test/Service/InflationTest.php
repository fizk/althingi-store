<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\Inflation;
use App\DatabaseConnectionTrait;
use DateTime;

class InflationTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreSimpleStructureCreate()
    {
        //GIVEN
        // ...

        //WHEN
        $result = (new Inflation())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'id' => 1,
                'date' => null,
                'value' => 1.45,
        ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Inflation::COLLECTION)->find([]),
            false
        );

        $expected = [
            new BSONDocument([
                '_id' => 1,
                'id' => 1,
                'date' => null,
                'value' => 1.45,
            ])
        ];
        $createdResultCode = 1;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreWithDateCreate()
    {
        //GIVEN
        // ...

        //WHEN
        $result = (new Inflation())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'id' => 1,
                'date' => '2001-01-01',
                'value' => 1.45,
        ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Inflation::COLLECTION)->find([]),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'id' => 1,
                'date' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'value' => 1.45,
            ])
        ];
        $createdResultCode = 1;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreNoAction()
    {
        //GIVEN
        $this->getDatabase()->selectCollection(Inflation::COLLECTION)->insertOne([
            '_id' => 1,
            'id' => 1,
            'date' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'value' => 1.45,
        ]);

        //WHEN
        $result = (new Inflation())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'id' => 1,
                'date' => '2001-01-01',
                'value' => 1.45,
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Inflation::COLLECTION)->find([]),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'id' => 1,
                'date' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'value' => 1.45,
            ])
        ];
        $createdResultCode = 0;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testGetNotFound()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Inflation::COLLECTION)->insertOne([
            '_id' => 1,
            'id' => 1,
            'date' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'value' => 1.23,
        ]);

        //WHEN
        $actual = (new Inflation())
            ->setSourceDatabase($this->getDatabase())
            ->get(2);

        //THEN
        $expected = null;

        $this->assertEquals($expected, $actual);
    }

    public function testGet()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Inflation::COLLECTION)->insertOne([
            '_id' => 1,
            'id' => 1,
            'date' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'value' => 1.23,
        ]);

        //WHEN
        $actual = (new Inflation())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        //THEN
        $expected = [
            '_id' => 1,
            'id' => 1,
            'date' => '2001-01-01T00:00:00+00:00',
            'value' => 1.23,
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Inflation::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'id' => 1,
                'date' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'value' => 1.23,
            ],
            [
                '_id' => 2,
                'id' => 2,
                'date' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'value' => 1.23,
            ],
        ]);

        //WHEN
        $actual = (new Inflation())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $expected = [
            [
                '_id' => 1,
                'id' => 1,
                'date' => '2001-01-01T00:00:00+00:00',
                'value' => 1.23,
            ], [
                '_id' => 2,
                'id' => 2,
                'date' => '2001-01-01T00:00:00+00:00',
                'value' => 1.23,
            ]
        ];

        $this->assertEquals($expected, $actual);
    }
}
