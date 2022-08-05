<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\Model\BSONArray;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\PlenaryAgenda;
use App\DatabaseConnectionTrait;
use DateTime;

class PlenaryAgendaTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStore()
    {
        //GIVEN

        //WHEN
        $result = (new PlenaryAgenda())
            ->setSourceDatabase($this->getDatabase())
            ->store(
                [
                    'item_id' => 1,
                    'plenary' => [
                        'plenary_id' => 2,
                        'assembly_id' => 3,
                        'from' => null,
                        'to' => null,
                        'name' => null,
                    ],
                    'issue' => [],
                    'assembly' => [
                        'assembly_id' => 4,
                        'from' => null,
                        'to' => null,
                    ],
                    'iteration_type' => 'Maybe<string>',
                    'iteration_continue' => 'Maybe<string>',
                    'iteration_comment' => 'Maybe<string>',
                    'comment' => 'Maybe<string>',
                    'comment_type' => 'Maybe<string>',

                    'posed' => [
                        'congressman_id' => 5,
                        'name' => 'string',
                        'birth' => '2001-01-01',
                        'death' => null,
                        'abbreviation' => null,
                    ],
                    'posed_party' => [
                        'party_id' => 6,
                        'name' => 'string',
                        'abbr_short' => null,
                        'abbr_long' => null,
                        'color' => null,
                    ],
                    'posed_constituency' => [
                        'constituency_id' => 7,
                        'name' => 'Maybe<string>',
                        'abbr_short' => null,
                        'abbr_long' => null,
                        'description' => null,
                    ],
                    'posed_title' => 'Maybe<string>',

                    'answerer' => [
                        'congressman_id' => 5,
                        'name' => 'string',
                        'birth' => '2001-01-01',
                        'death' => null,
                        'abbreviation' => null,
                    ],
                    'answerer_party' => [
                        'party_id' => 6,
                        'name' => 'string',
                        'abbr_short' => null,
                        'abbr_long' => null,
                        'color' => null,
                    ],
                    'answerer_constituency' => [
                        'constituency_id' => 7,
                        'name' => 'Maybe<string>',
                        'abbr_short' => null,
                        'abbr_long' => null,
                        'description' => null,
                    ],
                    'answerer_title' => 'Maybe<string>',

                    'counter_answerer' => [
                        'congressman_id' => 5,
                        'name' => 'string',
                        'birth' => '2001-01-01',
                        'death' => null,
                        'abbreviation' => null,
                    ],
                    'counter_answerer_party' => [
                        'party_id' => 6,
                        'name' => 'string',
                        'abbr_short' => null,
                        'abbr_long' => null,
                        'color' => null,
                    ],
                    'counter_answerer_constituency' => [
                        'constituency_id' => 7,
                        'name' => 'Maybe<string>',
                        'abbr_short' => null,
                        'abbr_long' => null,
                        'description' => null,
                    ],
                    'counter_answerer_title' => 'Maybe<string>',

                    'instigator' => [
                        'congressman_id' => 5,
                        'name' => 'string',
                        'birth' => '2001-01-01',
                        'death' => null,
                        'abbreviation' => null,
                    ],
                    'instigator_party' => [
                        'party_id' => 6,
                        'name' => 'string',
                        'abbr_short' => null,
                        'abbr_long' => null,
                        'color' => null,
                    ],
                    'instigator_constituency' => [
                        'constituency_id' => 7,
                        'name' => 'Maybe<string>',
                        'abbr_short' => null,
                        'abbr_long' => null,
                        'description' => null,
                    ],
                    'instigator_title' => 'Maybe<string>',
                ]
        );

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => new BSONDocument([
                    'assembly_id' => 4,
                    'plenary_id' => 2,
                    'item_id' => 1
                ]),
                'item_id' => 1,
                'plenary' => new BSONDocument([
                    'plenary_id' => 2,
                    'assembly_id' => 3,
                    'from' => null,
                    'to' => null,
                    'name' => null,
                ]),
                'issue' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' => 4,
                    'from' => null,
                    'to' => null,
                ]),
                'iteration_type' => 'Maybe<string>',
                'iteration_continue' => 'Maybe<string>',
                'iteration_comment' => 'Maybe<string>',
                'comment' => 'Maybe<string>',
                'comment_type' => 'Maybe<string>',

                'posed' => new BSONDocument([
                    'congressman_id' => 5,
                    'name' => 'string',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => null,
                ]),
                'posed_party' => new BSONDocument([
                    'party_id' => 6,
                    'name' => 'string',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'color' => null,
                ]),
                'posed_constituency' => new BSONDocument([
                    'constituency_id' => 7,
                    'name' => 'Maybe<string>',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'description' => null,
                ]),
                'posed_title' => 'Maybe<string>',

                'answerer' => new BSONDocument([
                    'congressman_id' => 5,
                    'name' => 'string',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => null,
                ]),
                'answerer_party' => new BSONDocument([
                    'party_id' => 6,
                    'name' => 'string',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'color' => null,
                ]),
                'answerer_constituency' => new BSONDocument([
                    'constituency_id' => 7,
                    'name' => 'Maybe<string>',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'description' => null,
                ]),
                'answerer_title' => 'Maybe<string>',

                'counter_answerer' => new BSONDocument([
                    'congressman_id' => 5,
                    'name' => 'string',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => null,
                ]),
                'counter_answerer_party' => new BSONDocument([
                    'party_id' => 6,
                    'name' => 'string',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'color' => null,
                ]),
                'counter_answerer_constituency' => new BSONDocument([
                    'constituency_id' => 7,
                    'name' => 'Maybe<string>',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'description' => null,
                ]),
                'counter_answerer_title' => 'Maybe<string>',

                'instigator' => new BSONDocument([
                    'congressman_id' => 5,
                    'name' => 'string',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => null,
                ]),
                'instigator_party' => new BSONDocument([
                    'party_id' => 6,
                    'name' => 'string',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'color' => null,
                ]),
                'instigator_constituency' => new BSONDocument([
                    'constituency_id' => 7,
                    'name' => 'Maybe<string>',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'description' => null,
                ]),
                'instigator_title' => 'Maybe<string>',
            ])
        ];
        $createdResultCode = 1;
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->find([]),
            false
        );

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testGet() {
        //GIVEN
        $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->insertOne([
            '_id' => [
                'assembly_id' => 1,
                'plenary_id' => 2,
                'item_id' => 3
            ],
            'answerer' => null,
            'answerer_constituency' => null,
            'answerer_party' => null,
            'answerer_title' => null,
            'assembly' => [
                'assembly_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ],
            'comment' => null,
            'comment_type' => null,
            'counter_answerer' => null,
            'counter_answerer_constituency' => null,
            'counter_answerer_party' => null,
            'counter_answerer_title' => null,
            'instigator' => null,
            'instigator_constituency' => null,
            'instigator_party' => null,
            'instigator_title' => null,
            'issue' => (object)[],
            'item_id' => 1,
            'iteration_comment' => null,
            'iteration_continue' => null,
            'iteration_type' => null,
            'plenary' => [
                'from' => null,
                'to' => null,
            ],
            'posed' => null,
            'posed_constituency' => null,
            'posed_party' => null,
            'posed_title' => null
        ]);

        //WHEN
        $actual = (new PlenaryAgenda())
            ->setSourceDatabase($this->getDatabase())
            ->get(1, 2, 3);

        // THEN
        $expected = [
            '_id' => [
                'assembly_id' => 1,
                'plenary_id' => 2,
                'item_id' => 3
            ],
            'answerer' => null,
            'answerer_constituency' => null,
            'answerer_party' => null,
            'answerer_title' => null,
            'assembly' => [
                'assembly_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
            'comment' => null,
            'comment_type' => null,
            'counter_answerer' => null,
            'counter_answerer_constituency' => null,
            'counter_answerer_party' => null,
            'counter_answerer_title' => null,
            'instigator' => null,
            'instigator_constituency' => null,
            'instigator_party' => null,
            'instigator_title' => null,
            'issue' => [
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
            ],
            'item_id' => 1,
            'iteration_comment' => null,
            'iteration_continue' => null,
            'iteration_type' => null,
            'plenary' => [
                'from' => null,
                'to' => null,
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
            ],
            'posed' => null,
            'posed_constituency' => null,
            'posed_party' => null,
            'posed_title' => null,
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testGetNotFound() {
        //GIVEN

        //WHEN
        $actual = (new PlenaryAgenda())
            ->setSourceDatabase($this->getDatabase())
            ->get(1, 2, 3);

        // THEN
        $expected = null;
        $this->assertEquals($expected, $actual);
    }

    public function testFetchByPlenary() {
        $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->insertOne([
            '_id' => [
                'assembly_id' => 1,
                'plenary_id' => 2,
                'item_id' => 3
            ],
            'answerer' => null,
            'answerer_constituency' => null,
            'answerer_party' => null,
            'answerer_title' => null,
            'assembly' => [
                'assembly_id' => 1,
                'from' => null,
                'to' => null
            ],
            'comment' => null,
            'comment_type' => null,
            'counter_answerer' => null,
            'counter_answerer_constituency' => null,
            'counter_answerer_party' => null,
            'counter_answerer_title' => null,
            'instigator' => null,
            'instigator_constituency' => null,
            'instigator_party' => null,
            'instigator_title' => null,
            'issue' => (object)[],
            'item_id' => 1,
            'iteration_comment' => null,
            'iteration_continue' => null,
            'iteration_type' => null,
            'plenary' => [
                'from' => null,
                'to' => null,
            ],
            'posed' => null,
            'posed_constituency' => null,
            'posed_party' => null,
            'posed_title' => null
        ]);

        //WHEN
        $actual = (new PlenaryAgenda())
            ->setSourceDatabase($this->getDatabase())
            ->fetchByPlenary(1, 2);

        // THEN
        $expected = [[
            '_id' => [
                'assembly_id' => 1,
                'plenary_id' => 2,
                'item_id' => 3
            ],
            'answerer' => null,
            'answerer_constituency' => null,
            'answerer_party' => null,
            'answerer_title' => null,
            'assembly' => [
                'assembly_id' => 1,
                'from' => null,
                'to' => null
            ],
            'comment' => null,
            'comment_type' => null,
            'counter_answerer' => null,
            'counter_answerer_constituency' => null,
            'counter_answerer_party' => null,
            'counter_answerer_title' => null,
            'instigator' => null,
            'instigator_constituency' => null,
            'instigator_party' => null,
            'instigator_title' => null,
            'issue' => [
                'assembly' => [
                'assembly_id' => 1,
                'from' => null,
                'to' => null
            ]],
            'item_id' => 1,
            'iteration_comment' => null,
            'iteration_continue' => null,
            'iteration_type' => null,
            'plenary' => [
                'from' => null,
                'to' => null,
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => null,
                    'to' => null
                ],
            ],
            'posed' => null,
            'posed_constituency' => null,
            'posed_party' => null,
            'posed_title' => null,
        ]];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateAssembly() {
        $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->insertMany([
            [
                '_id' => [
                    'assembly_id' => 1,
                    'plenary_id' => 1,
                    'item_id' => 3
                ],
                'answerer' => null,
                'answerer_constituency' => null,
                'answerer_party' => null,
                'answerer_title' => null,
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => null,
                    'to' => null
                ],
                'comment' => null,
                'comment_type' => null,
                'counter_answerer' => null,
                'counter_answerer_constituency' => null,
                'counter_answerer_party' => null,
                'counter_answerer_title' => null,
                'instigator' => null,
                'instigator_constituency' => null,
                'instigator_party' => null,
                'instigator_title' => null,
                'issue' => [],
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => null,
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null
            ],
            [
                '_id' => [
                    'assembly_id' => 2,
                    'plenary_id' => 2,
                    'item_id' => 3
                ],
                'answerer' => null,
                'answerer_constituency' => null,
                'answerer_party' => null,
                'answerer_title' => null,
                'assembly' => [
                    'assembly_id' => 2,
                    'from' => null,
                    'to' => null
                ],
                'comment' => null,
                'comment_type' => null,
                'counter_answerer' => null,
                'counter_answerer_constituency' => null,
                'counter_answerer_party' => null,
                'counter_answerer_title' => null,
                'instigator' => null,
                'instigator_constituency' => null,
                'instigator_party' => null,
                'instigator_title' => null,
                'issue' => [],
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => null,
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null
            ],
        ]);

        //WHEN
        (new PlenaryAgenda())
            ->setSourceDatabase($this->getDatabase())
            ->updateAssembly([
                'assembly_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01'
            ]);

        // THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->find([])
        );
        $expected = [
            new BSONDocument([
                '_id' => new BSONDocument([
                    'assembly_id' => 1,
                    'plenary_id' => 1,
                    'item_id' => 3
                ]),
                'answerer' => null,
                'answerer_constituency' => null,
                'answerer_party' => null,
                'answerer_title' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'comment' => null,
                'comment_type' => null,
                'counter_answerer' => null,
                'counter_answerer_constituency' => null,
                'counter_answerer_party' => null,
                'counter_answerer_title' => null,
                'instigator' => null,
                'instigator_constituency' => null,
                'instigator_party' => null,
                'instigator_title' => null,
                'issue' => new BSONArray([]),
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => null,
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null
            ]),
            new BSONDocument([
                '_id' => new BSONDocument([
                    'assembly_id' => 2,
                    'plenary_id' => 2,
                    'item_id' => 3
                ]),
                'answerer' => null,
                'answerer_constituency' => null,
                'answerer_party' => null,
                'answerer_title' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => null,
                    'to' => null
                ]),
                'comment' => null,
                'comment_type' => null,
                'counter_answerer' => null,
                'counter_answerer_constituency' => null,
                'counter_answerer_party' => null,
                'counter_answerer_title' => null,
                'instigator' => null,
                'instigator_constituency' => null,
                'instigator_party' => null,
                'instigator_title' => null,
                'issue' => new BSONArray([]),
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => null,
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null
            ]),
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdatePlenary() {
        $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->insertMany([
            [
                '_id' => [
                    'assembly_id' => 1,
                    'plenary_id' => 1,
                    'item_id' => 3
                ],
                'answerer' => null,
                'answerer_constituency' => null,
                'answerer_party' => null,
                'answerer_title' => null,
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => null,
                    'to' => null
                ],
                'comment' => null,
                'comment_type' => null,
                'counter_answerer' => null,
                'counter_answerer_constituency' => null,
                'counter_answerer_party' => null,
                'counter_answerer_title' => null,
                'instigator' => null,
                'instigator_constituency' => null,
                'instigator_party' => null,
                'instigator_title' => null,
                'issue' => [],
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => [
                    'plenary_id' => 1,
                    'assembly_id' => 152,
                    'from' => null,
                    'to' => null,
                    'name' => 'framhald þingsetningarfundar'
                ],
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null
            ],
            [
                '_id' => [
                    'assembly_id' => 2,
                    'plenary_id' => 2,
                    'item_id' => 3
                ],
                'answerer' => null,
                'answerer_constituency' => null,
                'answerer_party' => null,
                'answerer_title' => null,
                'assembly' => [
                    'assembly_id' => 2,
                    'from' => null,
                    'to' => null
                ],
                'comment' => null,
                'comment_type' => null,
                'counter_answerer' => null,
                'counter_answerer_constituency' => null,
                'counter_answerer_party' => null,
                'counter_answerer_title' => null,
                'instigator' => null,
                'instigator_constituency' => null,
                'instigator_party' => null,
                'instigator_title' => null,
                'issue' => [],
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => [
                    'plenary_id' => 1,
                    'assembly_id' => 151,
                    'from' => null,
                    'to' => null,
                    'name' => null
                ],
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null
            ],
        ]);

        //WHEN
        (new PlenaryAgenda())
            ->setSourceDatabase($this->getDatabase())
            ->updatePlenary([
                'plenary_id' => 1,
                'assembly_id' => 151,
                'from' => '2001-01-01 23:01:02',
                'to' => '2001-01-01 23:01:02',
                'name' => 'framhald update'
            ]);

        // THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->find([])
        );
        $expected = [
            new BSONDocument([
                '_id' => new BSONDocument([
                    'assembly_id' => 1,
                    'plenary_id' => 1,
                    'item_id' => 3
                ]),
                'answerer' => null,
                'answerer_constituency' => null,
                'answerer_party' => null,
                'answerer_title' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' => 1,
                    'from' => null,
                    'to' => null,
                ]),
                'comment' => null,
                'comment_type' => null,
                'counter_answerer' => null,
                'counter_answerer_constituency' => null,
                'counter_answerer_party' => null,
                'counter_answerer_title' => null,
                'instigator' => null,
                'instigator_constituency' => null,
                'instigator_party' => null,
                'instigator_title' => null,
                'issue' => new BSONArray([]),
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => new BSONDocument([
                    'plenary_id' => 1,
                    'assembly_id' => 152,
                    'from' => null,
                    'to' => null,
                    'name' => 'framhald þingsetningarfundar'
                ]),
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null
            ]),
            new BSONDocument([
                '_id' => new BSONDocument([
                    'assembly_id' => 2,
                    'plenary_id' => 2,
                    'item_id' => 3
                ]),
                'answerer' => null,
                'answerer_constituency' => null,
                'answerer_party' => null,
                'answerer_title' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' => 2,
                    'from' => null,
                    'to' => null
                ]),
                'comment' => null,
                'comment_type' => null,
                'counter_answerer' => null,
                'counter_answerer_constituency' => null,
                'counter_answerer_party' => null,
                'counter_answerer_title' => null,
                'instigator' => null,
                'instigator_constituency' => null,
                'instigator_party' => null,
                'instigator_title' => null,
                'issue' => new BSONArray([]),
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => new BSONDocument([
                    'plenary_id' => 1,
                    'assembly_id' => 151,
                    'from' => new UTCDateTime((new DateTime('2001-01-01 23:01:02'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01 23:01:02'))->getTimestamp() * 1000),
                    'name' => 'framhald update'
                ]),
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null
            ]),
        ];
        $this->assertEquals($expected, $actual);
    }
}
