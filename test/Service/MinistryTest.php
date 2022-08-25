<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\Ministry;
use App\DatabaseConnectionTrait;
use App\Presenter\MinistryPresenter;
use DateTime;

class MinistryTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreSimpleStructureCreate()
    {
        //GIVEN
        // ...

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
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Ministry::COLLECTION)->find([]),
            false
        );

        $expected = [
            new BSONDocument([
                '_id' => 1,
                'ministry_id' => 1,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => null,
                'last' => null,
            ])
        ];

        $createdResultCode = 1;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreCompleteStructureCreate()
    {
        //GIVEN
        // ...

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
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Ministry::COLLECTION)->find([]),
            false
        );

        $expected = [
            new BSONDocument([
                '_id' => 1,
                'ministry_id' => 1,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => new BSONDocument([
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'last' => new BSONDocument([
                    '_id' => 2,
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                ]),
            ])
        ];

        $createdResultCode = 1;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testGet()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Ministry::COLLECTION)->insertOne(
            (new MinistryPresenter)->serialize([
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
            ])
        );

        //WHEN
        // ...

        //THEN
        $actual = (new Ministry())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        $expected = [
            '_id' => 1,
            'ministry_id' => 1,
            'name' => 'name-1',
            'abbr_short' => 'abbr_short-1',
            'abbr_long' => 'abbr_long-1',
            'first' => [
                '_id' => 1,
                'assembly_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
            'last' => [
                '_id' => 2,
                'assembly_id' => 2,
                'from' => '2002-01-01T00:00:00+00:00',
                'to' => '2002-01-01T00:00:00+00:00',
            ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Ministry::COLLECTION)->insertMany([
            (new MinistryPresenter)->serialize([
                'ministry_id' => 1,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'last' => null,
            ]),
            (new MinistryPresenter)->serialize([
                'ministry_id' => 2,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2003-01-01',
                    'to' => '2003-01-01',
                ],
                'last' => [
                    'assembly_id' => 2,
                    'from' => '2004-01-01',
                    'to' => '2004-01-01',
                ],
            ])
        ]);

        //WHEN
        // ...

        //THEN
        $actual = (new Ministry())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        $expected = [
            [
                '_id' => 1,
                'ministry_id' => 1,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => [
                    '_id' => 1,
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
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2003-01-01T00:00:00+00:00',
                    'to' => '2003-01-01T00:00:00+00:00',
                ],
                'last' => [
                    '_id' => 2,
                    'assembly_id' => 2,
                    'from' => '2004-01-01T00:00:00+00:00',
                    'to' => '2004-01-01T00:00:00+00:00',
                ],
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateAssembly()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Ministry::COLLECTION)->insertMany([
            (new MinistryPresenter)->serialize([
                'ministry_id' => 1,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'last' => null,
            ]),
            (new MinistryPresenter)->serialize([
                'ministry_id' => 2,
                'name' => 'name-2',
                'abbr_short' => 'abbr_short-2',
                'abbr_long' => 'abbr_long-2',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2003-01-01',
                    'to' => '2003-01-01',
                ],
                'last' => [
                    'assembly_id' => 2,
                    'from' => '2004-01-01',
                    'to' => '2004-01-01',
                ],
            ])
        ]);

        //WHEN
        (new Ministry())
            ->setSourceDatabase($this->getDatabase())
            ->updateAssembly([
                'assembly_id' => 2,
                'from' => '1978-04-11',
                'to' => '1978-04-11',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Ministry::COLLECTION)->find(),
            true
        );

        $expected = [
            new BSONDocument([
                '_id' => 1,
                'ministry_id' => 1,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => new BSONDocument([
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'last' => null,
            ]),
            new BSONDocument([
                '_id' => 2,
                'ministry_id' => 2,
                'name' => 'name-2',
                'abbr_short' => 'abbr_short-2',
                'abbr_long' => 'abbr_long-2',
                'first' => new BSONDocument([
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                ]),
                'last' => new BSONDocument([
                    '_id' => 2,
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('1978-04-11'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('1978-04-11'))->getTimestamp() * 1000),
                ]),
            ])
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateAssemblies()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Ministry::COLLECTION)->insertMany([
            (new MinistryPresenter)->serialize([
                'ministry_id' => 1,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'last' => null,
            ]),
            (new MinistryPresenter)->serialize([
                'ministry_id' => 2,
                'name' => 'name-2',
                'abbr_short' => 'abbr_short-2',
                'abbr_long' => 'abbr_long-2',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2003-01-01',
                    'to' => '2003-01-01',
                ],
                'last' => [
                    'assembly_id' => 2,
                    'from' => '2004-01-01',
                    'to' => '2004-01-01',
                ],
            ])
        ]);

        //WHEN
        (new Ministry())
            ->setSourceDatabase($this->getDatabase())
            ->updateAssembly([
                'assembly_id' => 1,
                'from' => '2022-01-01',
                'to' => '2022-01-01',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Ministry::COLLECTION)->find(),
            true
        );

        $expected = [
            new BSONDocument([
                '_id' => 1,
                'ministry_id' => 1,
                'name' => 'name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => new BSONDocument([
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2022-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2022-01-01'))->getTimestamp() * 1000),
                ]),
                'last' => null,
            ]),
            new BSONDocument([
                '_id' => 2,
                'ministry_id' => 2,
                'name' => 'name-2',
                'abbr_short' => 'abbr_short-2',
                'abbr_long' => 'abbr_long-2',
                'first' => new BSONDocument([
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2022-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2022-01-01'))->getTimestamp() * 1000),
                ]),
                'last' => new BSONDocument([
                    '_id' => 2,
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2004-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2004-01-01'))->getTimestamp() * 1000),
                ]),
            ])
        ];

        $this->assertEquals($expected, $actual);
    }
}
