<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\PlenaryAgenda;
use App\DatabaseConnectionTrait;
use App\Presenter\PlenaryAgendaPresenter;
use DateTime;
use MongoDB\Model\BSONArray;

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
                        'assembly_id' => 4,
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
                    '_id' => new BSONDocument([
                        'plenary_id' => 2,
                        'assembly_id' => 4,
                    ]),
                    'plenary_id' => 2,
                    'from' => null,
                    'to' => null,
                    'name' => null,
                ]),
                'issue' => null,
                'assembly' => new BSONDocument([
                    '_id' => 4,
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
                    '_id' => 5,
                    'congressman_id' => 5,
                    'name' => 'string',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => null,
                ]),
                'posed_party' => new BSONDocument([
                    '_id' => 6,
                    'party_id' => 6,
                    'name' => 'string',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'color' => null,
                ]),
                'posed_constituency' => new BSONDocument([
                    '_id' => 7,
                    'constituency_id' => 7,
                    'name' => 'Maybe<string>',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'description' => null,
                ]),
                'posed_title' => 'Maybe<string>',
                'answerer' => new BSONDocument([
                    '_id' => 5,
                    'congressman_id' => 5,
                    'name' => 'string',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => null,
                ]),
                'answerer_party' => new BSONDocument([
                    '_id' => 6,
                    'party_id' => 6,
                    'name' => 'string',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'color' => null,
                ]),
                'answerer_constituency' => new BSONDocument([
                    '_id' => 7,
                    'constituency_id' => 7,
                    'name' => 'Maybe<string>',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'description' => null,
                ]),
                'answerer_title' => 'Maybe<string>',
                'counter_answerer' => new BSONDocument([
                    '_id' => 5,
                    'congressman_id' => 5,
                    'name' => 'string',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => null,
                ]),
                'counter_answerer_party' => new BSONDocument([
                    '_id' => 6,
                    'party_id' => 6,
                    'name' => 'string',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'color' => null,
                ]),
                'counter_answerer_constituency' => new BSONDocument([
                    '_id' => 7,
                    'constituency_id' => 7,
                    'name' => 'Maybe<string>',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'description' => null,
                ]),
                'counter_answerer_title' => 'Maybe<string>',
                'instigator' => new BSONDocument([
                    '_id' => 5,
                    'congressman_id' => 5,
                    'name' => 'string',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => null,
                ]),
                'instigator_party' => new BSONDocument([
                    '_id' => 6,
                    'party_id' => 6,
                    'name' => 'string',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'color' => null,
                ]),
                'instigator_constituency' => new BSONDocument([
                    '_id' => 7,
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
        $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->insertOne(
            (new PlenaryAgendaPresenter)->serialize([
                'iteration_type' => null,
                'iteration_continue' => null,
                'iteration_comment' => null,
                'comment' => null,
                'comment_type' => null,
                'posed' => null,
                'posed_party' => null,
                'posed_constituency' => null,
                'posed_title' => null,
                'answerer' => null,
                'answerer_party' => null,
                'answerer_constituency' => null,
                'answerer_title' => null,
                'counter_answerer' => null,
                'counter_answerer_party' => null,
                'counter_answerer_constituency' => null,
                'counter_answerer_title' => null,
                'instigator' => null,
                'instigator_party' => null,
                'instigator_constituency' => null,
                'instigator_title' => null,
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'issue' => [
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'category' => 'a',
                ],
                'item_id' => 3,
                'plenary' => [
                    'plenary_id' => 2,
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-02',
                    'name' => 'some name'
                ],
            ])
        );

        $i = iterator_to_array($this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->find());

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
                '_id' => 1,
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
                '_id' => [
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'category' => 'a',
                ],
                'issue_id' => 1,
                'category' => 'a',
                'assembly' => [
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'name' => null,
                'sub_name' => null,
                'type' => null,
                'type_name' => null,
                'type_subname' => null,
                'status' => null,
                'question' => null,
                'goal' => null,
                'major_changes' => null,
                'changes_in_law' => null,
                'costs_and_revenues' => null,
                'deliveries' => null,
                'additional_information' => null,
                'congressman' => null,
                'proponents' => [],
                'content_categories' => [],
                'content_super_categories' => [],
            ],
            'item_id' => 3,
            'iteration_comment' => null,
            'iteration_continue' => null,
            'iteration_type' => null,
            'plenary' => [
                '_id' => [
                    'assembly_id' => 1,
                    'plenary_id' => 2,
                ],
                'name' => 'some name',
                'plenary_id' => 2,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-02T00:00:00+00:00',
                'assembly' => [
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'duration' => 1440
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
        $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->insertOne(
            (new PlenaryAgendaPresenter)->serialize([
            'assembly' => [
                'assembly_id' => 1,
                'from' => null,
                'to' => null
            ],
            'issue' => [
                'assembly_id' => 1,
                'issue_id' => 1,
                'category' => 'a',
            ],
            'item_id' => 1,
            'plenary' => [
                'assembly_id' => 1,
                'plenary_id' => 2,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                'name' => 'some name',
            ],
        ]));

        //WHEN
        $actual = (new PlenaryAgenda())
            ->setSourceDatabase($this->getDatabase())
            ->fetchByPlenary(1, 2);

        // THEN
        $expected = [
            [
                '_id' => [
                    'assembly_id' => 1,
                    'plenary_id' => 2,
                    'item_id' => 1
                ],
                'answerer' => null,
                'answerer_constituency' => null,
                'answerer_party' => null,
                'answerer_title' => null,
                'assembly' => [
                    '_id' => 1,
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
                    '_id' => [
                        'assembly_id' => 1,
                        'issue_id' => 1,
                        'category' => 'a',
                    ],
                    'issue_id' => 1,
                    'category' => 'a',
                    'assembly' => [
                        '_id' => 1,
                        'assembly_id' => 1,
                        'from' => null,
                        'to' => null
                    ],
                    'name' => null,
                    'sub_name' => null,
                    'type' => null,
                    'type_name' => null,
                    'type_subname' => null,
                    'status' => null,
                    'question' => null,
                    'goal' => null,
                    'major_changes' => null,
                    'changes_in_law' => null,
                    'costs_and_revenues' => null,
                    'deliveries' => null,
                    'additional_information' => null,
                    'congressman' => null,
                    'proponents' => [],
                    'content_categories' => [],
                    'content_super_categories' => [],
                ],
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => [
                    '_id' => [
                        'assembly_id' => 1,
                        'plenary_id' => 2,
                    ],
                    'plenary_id' => 2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                    'duration' => 0,
                    'name' => 'some name',
                    'assembly' => [
                        '_id' => 1,
                        'assembly_id' => 1,
                        'from' => null,
                        'to' => null
                    ],
                ],
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null,
            ]
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateAssembly() {
        $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->insertMany([
            (new PlenaryAgendaPresenter)->serialize([
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
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'category' => 'a',
                ],
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => [
                    'assembly_id' => 1,
                    'plenary_id' => 1,
                ],
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null
            ]),
            (new PlenaryAgendaPresenter)->serialize([
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
                'issue' => [
                    'assembly_id' => 2,
                    'issue_id' => 2,
                    'category' => 'a',
                ],
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => [
                    'assembly_id' => 2,
                    'plenary_id' => 2,
                ],
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null
            ]),
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

        $expectedOneAssembly = new BSONDocument([
            '_id' => 1,
            'assembly_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
        ]);
        $expectedTwoAssembly = new BSONDocument([
            '_id' => 2,
            'assembly_id' => 2,
            'from' => null,
            'to' => null
        ]);

        $this->assertEquals($expectedOneAssembly, $actual[0]['assembly']);
        $this->assertEquals($expectedTwoAssembly, $actual[1]['assembly']);

    }

    public function testUpdatePlenary() {
        $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->insertMany([
            (new PlenaryAgendaPresenter)->serialize([
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
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'category' => 'a',
                ],
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => [
                    'assembly_id' => 1,
                    'plenary_id' => 1,
                    'from' => null,
                    'to' => null,
                    'name' => 'framhald þingsetningarfundar'
                ],
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null
            ]),
            (new PlenaryAgendaPresenter)->serialize([
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
                'issue' => [
                    'assembly_id' => 2,
                    'issue_id' => 2,
                    'category' => 'a',
                ],
                'item_id' => 1,
                'iteration_comment' => null,
                'iteration_continue' => null,
                'iteration_type' => null,
                'plenary' => [
                    'assembly_id' => 2,
                    'plenary_id' => 1,
                    'from' => null,
                    'to' => null,
                    'name' => null
                ],
                'posed' => null,
                'posed_constituency' => null,
                'posed_party' => null,
                'posed_title' => null
            ]),
        ]);

        //WHEN
        (new PlenaryAgenda())
            ->setSourceDatabase($this->getDatabase())
            ->updatePlenary([
                'plenary_id' => 1,
                'assembly_id' => 1,
                'from' => '2001-01-01 23:01:02',
                'to' => '2001-01-01 23:01:02',
                'name' => 'framhald update'
            ]);

        // THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->find([])
        );

        $expectedOnePlenary = new BSONDocument([
            '_id' => new BSONDocument([
                'assembly_id' => 1,
                'plenary_id' => 1,
            ]),
            'plenary_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01 23:01:02'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2001-01-01 23:01:02'))->getTimestamp() * 1000),
            'name' => 'framhald update'
        ]);
        $expectedTwoPlenary = new BSONDocument([
            '_id' => new BSONDocument([
                'assembly_id' => 2,
                'plenary_id' => 1,
            ]),
            'plenary_id' => 1,
            'from' => null,
            'to' => null,
            'name' => null
        ]);

        $this->assertEquals($expectedOnePlenary, $actual[0]['plenary']);
        $this->assertEquals($expectedTwoPlenary, $actual[1]['plenary']);
    }

    public function testUpdateIssue() {
        $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->insertMany([
            (new PlenaryAgendaPresenter)->serialize([
                'assembly' => [
                    'assembly_id' => 1,
                ],
                'plenary' => [
                    'plenary_id' => 1,
                ],
                'item_id' => 2,
                'issue' => [
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'category' => 'a',
                    'status' => 'one'
                ],
            ]),
            (new PlenaryAgendaPresenter)->serialize([
                'assembly' => [
                    'assembly_id' => 2,
                ],
                'plenary' => [
                    'plenary_id' => 2,

                ],
                'item_id' => 3,
                'issue' => [
                    'issue_id' => 2,
                    'assembly_id' => 1,
                    'category' => 'a',
                    'status' => 'one'
                ],
            ]),
        ]);

        //WHEN
        (new PlenaryAgenda())
            ->setSourceDatabase($this->getDatabase())
            ->updateIssue([
                'issue_id' => 1,
                'assembly_id' => 1,
                'category' => 'a',
                'status' => 'update'
            ]);

        // THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(PlenaryAgenda::COLLECTION)->find([])
        );

        $expectedIssueOne = new BSONDocument([
            '_id' => new BSONDocument([
                'assembly_id' => 1,
                'issue_id' => 1,
                'category' => 'a',
            ]),
            'category' => 'a',
            'status' => 'update',
            'issue_id' => 1,
            'name' => null,
            'sub_name' => null,
            'type' => null,
            'type_name' => null,
            'type_subname' => null,
            'question' => null,
            'goal' => null,
            'major_changes' => null,
            'changes_in_law' => null,
            'costs_and_revenues' => null,
            'deliveries' => null,
            'additional_information' => null,
            'assembly' => null,
            'congressman' => null,
            'proponents' => new BSONArray([]),
            'content_categories' => new BSONArray([]),
            'content_super_categories' => new BSONArray([]),
        ]);
        $expectedIssueTwo = new BSONDocument([
            '_id' => new BSONDocument([
                'assembly_id' => 1,
                'issue_id' => 2,
                'category' => 'a',
            ]),
            'category' => 'a',
            'status' => 'one',
            'issue_id' => 2,
            'name' => null,
            'sub_name' => null,
            'type' => null,
            'type_name' => null,
            'type_subname' => null,
            'question' => null,
            'goal' => null,
            'major_changes' => null,
            'changes_in_law' => null,
            'costs_and_revenues' => null,
            'deliveries' => null,
            'additional_information' => null,
            'assembly' => null,
            'congressman' => null,
            'proponents' => new BSONArray([]),
            'content_categories' => new BSONArray([]),
            'content_super_categories' => new BSONArray([]),
        ]);

        $this->assertEquals($expectedIssueOne, $actual[0]['issue']);
        $this->assertEquals($expectedIssueTwo, $actual[1]['issue']);
    }
}
