<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\Inflation;
use App\DatabaseConnectionTrait;
use App\Presenter\InflationPresenter;
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
        $this->getDatabase()->selectCollection(Inflation::COLLECTION)->insertOne(
            (new InflationPresenter)->serialize([
                'id' => 1,
                'date' => '2001-01-01',
                'value' => 1.45,
            ])
        );

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
        $this->getDatabase()->selectCollection(Inflation::COLLECTION)->insertOne(
            (new InflationPresenter)->serialize([
            'id' => 1,
            'date' => '2001-01-01',
            'value' => 1.23,
        ]));

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
        $this->getDatabase()->selectCollection(Inflation::COLLECTION)->insertOne(
            (new InflationPresenter)->serialize([
                'id' => 1,
                'date' => '2001-01-01',
                'value' => 1.23,
            ])
        );

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
            (new InflationPresenter)->serialize([
                'id' => 1,
                'date' => '2001-01-01',
                'value' => 1.23,
            ]),
            (new InflationPresenter)->serialize([
                'id' => 2,
                'date' => '2001-01-01',
                'value' => 1.23,
            ]),
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

    public function testFetchRangeOne()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Inflation::COLLECTION)->insertMany([
            (new InflationPresenter)->serialize([
                'id' => 1,
                'date' => '2001-01-01',
                'value' => 1.23,
            ]),
            (new InflationPresenter)->serialize([
                'id' => 2,
                'date' => '2001-02-01',
                'value' => 1.23,
            ]),
        ]);

        //WHEN
        $actual = (new Inflation())
            ->setSourceDatabase($this->getDatabase())
            ->fetchRange(
                new DateTime('2001-01-01'),
                new DateTime('2001-01-20'),
            );

        //THEN
        $expected = [
            [
                '_id' => 1,
                'id' => 1,
                'date' => '2001-01-01T00:00:00+00:00',
                'value' => 1.23,
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testFetchRangeTwo()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Inflation::COLLECTION)->insertMany([
            (new InflationPresenter)->serialize([
                'id' => 1,
                'date' => '2001-01-01',
                'value' => 1.23,
            ]),
            (new InflationPresenter)->serialize([
                'id' => 2,
                'date' => '2001-02-01',
                'value' => 1.23,
            ]),
        ]);

        //WHEN
        $actual = (new Inflation())
            ->setSourceDatabase($this->getDatabase())
            ->fetchRange(
                new DateTime('2001-01-02'),
                new DateTime('2002-02-01'),
            );

        //THEN
        $expected = [
            [
                '_id' => 2,
                'id' => 2,
                'date' => '2001-02-01T00:00:00+00:00',
                'value' => 1.23,
            ]
        ];

        $this->assertEquals($expected, $actual);
    }
    public function testFetchRange()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Inflation::COLLECTION)->insertMany([
            (new InflationPresenter)->serialize([
                'id' => 1,
                'date' => '2001-01-01',
                'value' => 1.23,
            ]),
            (new InflationPresenter)->serialize([
                'id' => 2,
                'date' => '2001-02-01',
                'value' => 1.23,
            ]),
        ]);

        //WHEN
        $actual = (new Inflation())
            ->setSourceDatabase($this->getDatabase())
            ->fetchRange(
                new DateTime('2000-01-01'),
                new DateTime('2002-02-01'),
            );

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
                'date' => '2001-02-01T00:00:00+00:00',
                'value' => 1.23,
            ]
        ];

        $this->assertEquals($expected, $actual);
    }
}
