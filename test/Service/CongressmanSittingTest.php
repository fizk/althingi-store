<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\CongressmanSitting;
use App\DatabaseConnectionTrait;
use DateTime;

use function PHPUnit\Framework\assertEquals;

class CongressmanSittingTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreSimpleStructureCreate()
    {
        //GIVEN
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'session_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2004-01-01'))->getTimestamp() * 1000),
                ]),
                'congressman' => new BSONDocument([
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ]),
                'congressman_constituency' => new BSONDocument([
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ]),
                'congressman_party' => new BSONDocument([
                    'party_id' => 5,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ])
            ])
        ];
        $createdResultCode = 1;

        //WHEN
        $result = (new CongressmanSitting())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'session_id' => 1,
                'from' => '2001-01-01',
                'to' => '2002-01-01',
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' => [
                    'assembly_id' => 2,
                    'from' => '2003-01-01',
                    'to' => '2004-01-01',
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => '2005-01-01',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_constituency' => [
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'congressman_party' => [
                    'party_id' => 5,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ]
        ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(CongressmanSitting::COLLECTION)->find([]),
            false
        );

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testGet()
    {
        //GIVE
        $this->getDatabase()->selectCollection(CongressmanSitting::COLLECTION)->insertOne([
            '_id' => 1,
            'session_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
            'type' => 'type',
            'abbr' => 'appr',
            'assembly' =>  [
                'assembly_id' => 2,
                'from' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2004-01-01'))->getTimestamp() * 1000),
            ],
            'congressman' =>  [
                'congressman_id' => 3,
                'name' => 'name',
                'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
                'death' => null,
                'abbreviation' => 'abbreviation',
            ],
            'congressman_constituency' =>  [
                'constituency_id' => 4,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
            ],
            'congressman_party' =>  [
                'party_id' => 5,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'color' => 'color',
            ]
        ]);

        //WHEN
        $actual = (new CongressmanSitting())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        //THEN
        $expected = [
            '_id' => 1,
            'session_id' => 1,
            'from' => '2001-01-01T00:00:00+00:00',
            'to' => '2002-01-01T00:00:00+00:00',
            'type' => 'type',
            'abbr' => 'appr',
            'assembly' =>  [
                'assembly_id' => 2,
                'from' => '2003-01-01T00:00:00+00:00',
                'to' => '2004-01-01T00:00:00+00:00',
            ],
            'congressman' =>  [
                'congressman_id' => 3,
                'name' => 'name',
                'birth' => '2005-01-01T00:00:00+00:00',
                'death' => null,
                'abbreviation' => 'abbreviation',
            ],
            'congressman_constituency' =>  [
                'constituency_id' => 4,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
            ],
            'congressman_party' =>  [
                'party_id' => 5,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'color' => 'color',
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testGetNotFound()
    {
        //GIVE
        $this->getDatabase()->selectCollection(CongressmanSitting::COLLECTION)->insertOne([
            '_id' => 1,
            'session_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
            'type' => 'type',
            'abbr' => 'appr',
            'assembly' =>  [
                'assembly_id' => 2,
                'from' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2004-01-01'))->getTimestamp() * 1000),
            ],
            'congressman' =>  [
                'congressman_id' => 3,
                'name' => 'name',
                'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
                'death' => null,
                'abbreviation' => 'abbreviation',
            ],
            'congressman_constituency' =>  [
                'constituency_id' => 4,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
            ],
            'congressman_party' =>  [
                'party_id' => 5,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'color' => 'color',
            ]
        ]);

        //WHEN
        $actual = (new CongressmanSitting())
            ->setSourceDatabase($this->getDatabase())
            ->get(2);

        //THEN
        $expected = null;

        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $this->getDatabase()->selectCollection(CongressmanSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'session_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2004-01-01'))->getTimestamp() * 1000),
                ],
                'congressman' =>  [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_constituency' =>  [
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'congressman_party' =>  [
                    'party_id' => 5,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ]
            ], [
                '_id' => 2,
                'session_id' => 2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2004-01-01'))->getTimestamp() * 1000),
                ],
                'congressman' =>  [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_constituency' =>  [
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'congressman_party' =>  [
                    'party_id' => 5,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ]
            ]
        ]);

        //WHEN
        $actual = (new CongressmanSitting())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $expected = [
            [
                '_id' => 1,
                'session_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2002-01-01T00:00:00+00:00',
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  [
                    'assembly_id' => 2,
                    'from' => '2003-01-01T00:00:00+00:00',
                    'to' => '2004-01-01T00:00:00+00:00',
                ],
                'congressman' =>  [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => '2005-01-01T00:00:00+00:00',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_constituency' =>  [
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'congressman_party' =>  [
                    'party_id' => 5,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ]
            ], [

                '_id' => 2,
                'session_id' => 2,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2002-01-01T00:00:00+00:00',
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  [
                    'assembly_id' => 2,
                    'from' => '2003-01-01T00:00:00+00:00',
                    'to' => '2004-01-01T00:00:00+00:00',
                ],
                'congressman' =>  [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => '2005-01-01T00:00:00+00:00',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_constituency' =>  [
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'congressman_party' =>  [
                    'party_id' => 5,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ]
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateAssembly()
    {
        //GIVEN
        $this->getDatabase()->selectCollection(CongressmanSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'session_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                    'to' => null,
                ],
                'congressman' =>  null,
                'congressman_constituency' =>  null,
                'congressman_party' =>  null
            ],
            [
                '_id' => 2,
                'session_id' => 2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                    'to' => null,
                ],
                'congressman' =>  null,
                'congressman_constituency' =>  null,
                'congressman_party' =>  null
            ],
            [
                '_id' => 3,
                'session_id' => 3,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                    'to' => null,
                ],
                'congressman' =>  null,
                'congressman_constituency' =>  null,
                'congressman_party' =>  null
            ],
        ]);

        //WHEN
        (new CongressmanSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateAssembly([
                'assembly_id' => 2,
                'from' => '1978-04-11',
                'to' => '1980-04-11',
            ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'session_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('1978-04-11'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('1980-04-11'))->getTimestamp() * 1000),
                ]),
                'congressman' =>  null,
                'congressman_constituency' =>  null,
                'congressman_party' =>  null
            ]),
            new BSONDocument([
                '_id' => 2,
                'session_id' => 2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  new BSONDocument([
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                    'to' => null,
                ]),
                'congressman' =>  null,
                'congressman_constituency' =>  null,
                'congressman_party' =>  null
            ]),
            new BSONDocument([
                '_id' => 3,
                'session_id' => 3,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('1978-04-11'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('1980-04-11'))->getTimestamp() * 1000),
                ]),
                'congressman' =>  null,
                'congressman_constituency' =>  null,
                'congressman_party' =>  null
            ]),
        ];
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(CongressmanSitting::COLLECTION)->find([]),
            false
        );

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateParty()
    {
        //GIVEN
        $this->getDatabase()->selectCollection(CongressmanSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'session_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  null,
                'congressman' =>  null,
                'congressman_constituency' =>  null,
                'congressman_party' => [
                    'party_id' => 1,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => null,
                ],
            ],
            [
                '_id' => 2,
                'session_id' => 2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' => null,
                'congressman' =>  null,
                'congressman_constituency' =>  null,
                'congressman_party' => [
                    'party_id' => 2,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => null,
                ],
            ],
            [
                '_id' => 3,
                'session_id' => 3,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' => null,
                'congressman' =>  null,
                'congressman_constituency' =>  null,
                'congressman_party' => [
                    'party_id' => 1,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => null,
                ],
            ],
        ]);

        //WHEN
        (new CongressmanSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateParty([
                'party_id' => 1,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'color' => '#ffffff',
            ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'session_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  null,
                'congressman' =>  null,
                'congressman_constituency' =>  null,
                'congressman_party' => new BSONDocument([
                    'party_id' => 1,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => '#ffffff',
                ]),
            ]),
            new BSONDocument([
                '_id' => 2,
                'session_id' => 2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  null,
                'congressman' =>  null,
                'congressman_constituency' =>  null,
                'congressman_party' => new BSONDocument([
                    'party_id' => 2,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => null,
                ]),
            ]),
            new BSONDocument([
                '_id' => 3,
                'session_id' => 3,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' => null,
                'congressman' =>  null,
                'congressman_constituency' =>  null,
                'congressman_party' => new BSONDocument([
                    'party_id' => 1,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => '#ffffff',
                ]),
            ]),
        ];
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(CongressmanSitting::COLLECTION)->find([]),
            false
        );

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateConstituency()
    {
        //GIVEN
        $this->getDatabase()->selectCollection(CongressmanSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'session_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  null,
                'congressman' =>  null,
                'congressman_constituency' =>  [
                    'constituency_id' => 1,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => null,
                ],
                'congressman_party' => null,
            ],
            [
                '_id' => 2,
                'session_id' => 2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' => null,
                'congressman' =>  null,
                'congressman_constituency' =>  [
                    'constituency_id' => 2,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => null,
                ],
                'congressman_party' => null,
            ],
            [
                '_id' => 3,
                'session_id' => 3,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' => null,
                'congressman' =>  null,
                'congressman_constituency' =>  [
                    'constituency_id' => 1,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => null,
                ],
                'congressman_party' => null,
            ],
        ]);

        //WHEN
        (new CongressmanSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateConstituency([
                'constituency_id' => 1,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
            ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'session_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  null,
                'congressman' =>  null,
                'congressman_constituency' => new BSONDocument([
                    'constituency_id' => 1,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ]),
                'congressman_party' => null,
            ]),
            new BSONDocument([
                '_id' => 2,
                'session_id' => 2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  null,
                'congressman' =>  null,
                'congressman_constituency' =>  new BSONDocument([
                    'constituency_id' => 2,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => null,
                ]),
                'congressman_party' => null,
            ]),
            new BSONDocument([
                '_id' => 3,
                'session_id' => 3,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' => null,
                'congressman' =>  null,
                'congressman_constituency' =>  new BSONDocument([
                    'constituency_id' => 1,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ]),
                'congressman_party' => null,
            ]),
        ];
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(CongressmanSitting::COLLECTION)->find([]),
            false
        );

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateCongressman()
    {
        //GIVEN
        $this->getDatabase()->selectCollection(CongressmanSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'session_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  null,
                'congressman_constituency' =>  null,
                'congressman' =>  [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => null,
            ],
            [
                '_id' => 2,
                'session_id' => 2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  null,
                'congressman_constituency' =>  null,
                'congressman' =>  [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => null,
            ],
            [
                '_id' => 3,
                'session_id' => 3,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  null,
                'congressman_constituency' =>  null,
                'congressman' =>  [
                    'congressman_id' => 2,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => null,
            ],
        ]);

        //WHEN
        (new CongressmanSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateCongressman([
                'congressman_id' => 2,
                'name' => 'name-edit',
                'birth' => '1978-04-11',
                'death' => null,
                'abbreviation' => 'abbreviation',
            ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'session_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  null,
                'congressman_constituency' =>  null,
                'congressman' =>  new BSONDocument([
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ]),
                'congressman_party' => null,
            ]),
            new BSONDocument([
                '_id' => 2,
                'session_id' => 2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  null,
                'congressman_constituency' =>  null,
                'congressman' =>  new BSONDocument([
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ]),
                'congressman_party' => null,
            ]),
            new BSONDocument([
                '_id' => 3,
                'session_id' => 3,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'type' => 'type',
                'abbr' => 'appr',
                'assembly' =>  null,
                'congressman_constituency' =>  null,
                'congressman' =>  new BSONDocument([
                    'congressman_id' => 2,
                    'name' => 'name-edit',
                    'birth' => new UTCDateTime((new DateTime('1978-04-11'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ]),
                'congressman_party' => null,
            ]),
        ];
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(CongressmanSitting::COLLECTION)->find([]),
            false
        );

        $this->assertEquals($expected, $actual);
    }
}
