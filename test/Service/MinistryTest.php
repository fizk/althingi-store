<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\Ministry;
use App\DatabaseConnectionTrait;
use DateTime;

class MinistryTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreSimpleStructureCreate()
    {
        //GIVEN
        $expected = [ [
            '_id' => 1,
            'ministry_id' => 1,
            'name' => 'name-1',
            'abbr_short' => 'abbr_short-1',
            'abbr_long' => 'abbr_long-1',
            'first' => null,
            'last' => null,

        ]];
        $createdResultCode = 1;

        //WHEN
        $result = (new Ministry())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'ministry_id' => 1,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => null,
                'last' => null,
        ]);

        //THEN
        $actual = array_map(function(BSONDocument $item) {
            return $item->getArrayCopy();
        }, iterator_to_array($this->getDatabase()->selectCollection(Ministry::COLLECTION)->find([]), false));

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreCompleteStructureCreate()
    {
        //GIVEN
        $expected = [ [
            '_id' => 1,
            'ministry_id' => 1,
            'name' => 'name-1',
            'abbr_short' => 'abbr_short-1',
            'abbr_long' => 'abbr_long-1',
            'first' => new BSONDocument([
                'assembly_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ]),
            'last' => new BSONDocument([
                'assembly_id' => 2,
                'from' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
            ]),
        ] ];
        $createdResultCode = 1;

        //WHEN
        $result = (new Ministry())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'ministry_id' => 1,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'last' => [
                    'assembly_id' => 2,
                    'from' => '2002-01-01',
                    'to' => '2002-01-01',
                ],
        ]);

        //THEN
        $actual = array_map(function(BSONDocument $item) {
            return $item->getArrayCopy();
        }, iterator_to_array($this->getDatabase()->selectCollection(Ministry::COLLECTION)->find([]), false));

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testGet()
    {
        //GIVE
        $expected = [
            '_id' => 1,
            'ministry_id' => 1,
            'name' => 'name-1',
            'abbr_short' => 'abbr_short-1',
            'abbr_long' => 'abbr_long-1',
            'first' => [
                'assembly_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
            'last' => [
                'assembly_id' => 2,
                'from' => '2002-01-01T00:00:00+00:00',
                'to' => '2002-01-01T00:00:00+00:00',
            ],
        ];

        $this->getDatabase()->selectCollection(Ministry::COLLECTION)->insertOne([
            '_id' => 1,
            'ministry_id' => 1,
            'name' => 'name-1',
            'abbr_short' => 'abbr_short-1',
            'abbr_long' => 'abbr_long-1',
            'first' => [
                'assembly_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ],
            'last' => [
                'assembly_id' => 2,
                'from' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
            ],
        ]);

        //WHEN
        $actual = (new Ministry())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        //THEN
        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $expected = [
            [
                '_id' => 1,
                'ministry_id' => 1,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'last' => null,
            ], [
                '_id' => 2,
                'ministry_id' => 2,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2003-01-01T00:00:00+00:00',
                    'to' => '2003-01-01T00:00:00+00:00',
                ],
                'last' => [
                    'assembly_id' => 2,
                    'from' => '2004-01-01T00:00:00+00:00',
                    'to' => '2004-01-01T00:00:00+00:00',
                ],
            ]
        ];

        $this->getDatabase()->selectCollection(Ministry::COLLECTION)->insertMany([[
                '_id' => 1,
                'ministry_id' => 1,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => [
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last' => null,
            ], [
                '_id' => 2,
                'ministry_id' => 2,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => [
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                ],
                'last' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2004-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2004-01-01'))->getTimestamp() * 1000),
                ],
            ]
        ]);


        //WHEN
        $actual = (new Ministry())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $this->assertEquals($expected, $actual);
    }
}
