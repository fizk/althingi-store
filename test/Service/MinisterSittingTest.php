<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\MinisterSitting;
use App\DatabaseConnectionTrait;
use DateTime;

class MinisterSittingTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testCreate()
    {
        //GIVEN
        // ...

        //WHEN
        $result = (new MinisterSitting())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'minister_sitting_id' => 1,
                'from' => '2001-01-01',
                'to' => null,
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'ministry' => [
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
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
                ],
                'first_ministry_assembly' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'last_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
        ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->find([]),
            false
        );
        $expected = [
            new BSONDocument([
            '_id' => 1,
            'minister_sitting_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => null,
            'assembly' => new BSONDocument([
                'assembly_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ]),
            'ministry' => new BSONDocument([
                'ministry_id' => 2,
                'name' => 'ministry-name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => 1,
                'last' => 2,
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
            ]),
            'first_ministry_assembly' => new BSONDocument([
                'assembly_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ]),
            'last_ministry_assembly' => new BSONDocument([
                'assembly_id' => 2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ]),
        ])];
        $createdResultCode = 1;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testGet()
    {
        //GIVE
        $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->insertOne([
            '_id' => 1,
            'minister_sitting_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => null,
            'assembly' => [
                'assembly_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ],
            'ministry' => [
                'ministry_id' => 2,
                'name' => 'ministry-name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => 1,
                'last' => 2,
            ],
            'congressman' => [
                'congressman_id' => 3,
                'name' => 'name',
                'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
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
            ],
            'first_ministry_assembly' => [
                'assembly_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ],
            'last_ministry_assembly' => [
                'assembly_id' => 2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ],
        ]);

        //WHEN
        $actual = (new MinisterSitting())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        //THEN
        $expected = [
            '_id' => 1,
            'minister_sitting_id' => 1,
            'from' => '2001-01-01T00:00:00+00:00',
            'to' => null,
            'assembly' => [
                'assembly_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
            'ministry' => [
                'ministry_id' => 2,
                'name' => 'ministry-name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => 1,
                'last' => 2,
            ],
            'congressman' => [
                'congressman_id' => 3,
                'name' => 'name',
                'birth' => '2005-01-01T00:00:00+00:00',
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
            ],
            'first_ministry_assembly' => [
                'assembly_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
            'last_ministry_assembly' => [
                'assembly_id' => 2,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'ministry' => [
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
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
                ],
                'first_ministry_assembly' => [
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ]
        ]);

        //WHEN
        $actual = (new MinisterSitting())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $expected =[ [
            '_id' => 1,
            'minister_sitting_id' => 1,
            'from' => '2001-01-01T00:00:00+00:00',
            'to' => null,
            'assembly' => [
                'assembly_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
            'ministry' => [
                'ministry_id' => 2,
                'name' => 'ministry-name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => 1,
                'last' => 2,
            ],
            'congressman' => [
                'congressman_id' => 3,
                'name' => 'name',
                'birth' => '2005-01-01T00:00:00+00:00',
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
            ],
            'first_ministry_assembly' => [
                'assembly_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
            'last_ministry_assembly' => [
                'assembly_id' => 2,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
        ]];
        $this->assertEquals($expected, $actual);
    }

    public function testFetchGovernmentPartiesByAssembly()
    {
        //GIVE
        $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'ministry' => [
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
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
                ],
                'first_ministry_assembly' => [
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ]
        ]);

        //WHEN
        $actual = (new MinisterSitting())
            ->setSourceDatabase($this->getDatabase())
            ->fetchGovernmentPartiesByAssembly(1);

        //THEN
        $expected =[ [
            'party_id' => 5,
            'name' => 'name',
            'abbr_short' => 'abbr_short',
            'abbr_long' => 'abbr_long',
            'color' => 'color',
        ]];
        $this->assertEquals($expected, $actual);
    }

    public function testFetchGovernmentSessionsByAssembly()
    {
        //GIVE
        $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'ministry' => [
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
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
                ],
                'first_ministry_assembly' => [
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ]
        ]);

        //WHEN
        $actual = (new MinisterSitting())
            ->setSourceDatabase($this->getDatabase())
            ->fetchGovernmentSessionsByAssembly(1);

        //THEN
        $expected = [
            [
                '_id' => 2,
                'ministry_id' => 2,
                'name' => 'ministry-name-1',
                'abbr_short' => 'abbr_short-1',
                'abbr_long' => 'abbr_long-1',
                'first' => 1,
                'last' => 2,
                'first_ministry_assembly' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'last_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'congressmen' => [
                    [
                        '_id' => 1,
                        'minister_sitting_id' => 1,
                        'from' => '2001-01-01T00:00:00+00:00',
                        'to' => null,
                        'assembly' => [
                            'assembly_id' => 1,
                            'from' => '2001-01-01T00:00:00+00:00',
                            'to' => '2001-01-01T00:00:00+00:00',
                        ],
                        'ministry' => [
                            'ministry_id' => 2,
                            'name' => 'ministry-name-1',
                            'abbr_short' => 'abbr_short-1',
                            'abbr_long' => 'abbr_long-1',
                            'first' => 1,
                            'last' => 2,
                        ],
                        'congressman' => [
                            'congressman_id' => 3,
                            'name' => 'name',
                            'birth' => '2005-01-01T00:00:00+00:00',
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
                        ],
                    ]
                ],
            ]
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateAssembly()
    {
        //GIVE
        $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'ministry' => [
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
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
                ],
                'first_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ]
        ]);

        //WHEN
        (new MinisterSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateAssembly([
                'assembly_id' => 2,
                'from' => '1978-01-01',
                'to' => '1978-01-01',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->find(),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                ]),
                'ministry' => new BSONDocument([
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
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
                ]),
                'first_ministry_assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                ]),
                'last_ministry_assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                ]),
            ])
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateParty()
    {
        //GIVE
        $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'ministry' => [
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
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
                ],
                'first_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ]
        ]);

        //WHEN
        (new MinisterSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateParty([
                'party_id' => 5,
                'name' => 'name-updated',
                'abbr_short' => 'abbr_short-updated',
                'abbr_long' => 'abbr_long-updated',
                'color' => 'color-updated',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->find(),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'ministry' => new BSONDocument([
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
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
                    'name' => 'name-updated',
                    'abbr_short' => 'abbr_short-updated',
                    'abbr_long' => 'abbr_long-updated',
                    'color' => 'color-updated',
                ]),
                'first_ministry_assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'last_ministry_assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
            ])
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateConstituency()
    {
        //GIVE
        $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'ministry' => [
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
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
                ],
                'first_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ]
        ]);

        //WHEN
        (new MinisterSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateConstituency([
                'constituency_id' => 4,
                'name' => 'name-updated',
                'abbr_short' => 'abbr_short-updated',
                'abbr_long' => 'abbr_long-updated',
                'description' => 'description-updated',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->find(),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'ministry' => new BSONDocument([
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
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
                    'name' => 'name-updated',
                    'abbr_short' => 'abbr_short-updated',
                    'abbr_long' => 'abbr_long-updated',
                    'description' => 'description-updated',
                ]),
                'congressman_party' => new BSONDocument([
                    'party_id' => 5,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ]),
                'first_ministry_assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'last_ministry_assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
            ])
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateMinistry()
    {
        //GIVE
        $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'ministry' => [
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
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
                ],
                'first_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ]
        ]);

        //WHEN
        (new MinisterSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateMinistry([
                'ministry_id' => 2,
                'name' => 'ministry-name-1-update',
                'abbr_short' => 'abbr_short-1-update',
                'abbr_long' => 'abbr_long-1-update',
                'first' => 10,
                'last' => 20,
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->find(),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'ministry' => new BSONDocument([
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1-update',
                    'abbr_short' => 'abbr_short-1-update',
                    'abbr_long' => 'abbr_long-1-update',
                    'first' => 10,
                    'last' => 20,
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
                ]),
                'first_ministry_assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'last_ministry_assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
            ])
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateCongressman()
    {
        //GIVE
        $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'ministry' => [
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2005-01-01'))->getTimestamp() * 1000),
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
                ],
                'first_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_ministry_assembly' => [
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ]
        ]);

        //WHEN
        (new MinisterSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateCongressman([
                'congressman_id' => 3,
                'name' => 'name-update',
                'birth' => '1978-04-11',
                'death' => null,
                'abbreviation' => 'abbreviation-update',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(MinisterSitting::COLLECTION)->find(),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'minister_sitting_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'ministry' => new BSONDocument([
                    'ministry_id' => 2,
                    'name' => 'ministry-name-1',
                    'abbr_short' => 'abbr_short-1',
                    'abbr_long' => 'abbr_long-1',
                    'first' => 1,
                    'last' => 2,
                ]),
                'congressman' => new BSONDocument([
                    'congressman_id' => 3,
                    'name' => 'name-update',
                    'birth' => new UTCDateTime((new DateTime('1978-04-11'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation-update',
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
                ]),
                'first_ministry_assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'last_ministry_assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
            ])
        ];
        $this->assertEquals($expected, $actual);
    }
}
