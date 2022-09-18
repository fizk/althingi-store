<?php

namespace App\Service;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\{BSONDocument, BSONArray};
use PHPUnit\Framework\TestCase;
use App\Service\Document;
use App\DatabaseConnectionTrait;
use App\Presenter\DocumentOutcomePresenter;
use App\Presenter\DocumentPresenter;
use DateTime;

class DocumentTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testGet()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Document::COLLECTION)->insertOne(
            (new DocumentPresenter)->serialize([
                'document_id' => 3,
                'issue' => [
                    'issue_id' => 2,
                    'assembly_id' => 1,
                    'category' => 'a'
                ],
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                ],
                'date' => '2001-01-01',
                'url' => 'url',
                'type' => 'type',
                'proponents' => [],
                'votes' => [],
            ])
        );

        //WHEN
        $actual = (new Document())
            ->setSourceDatabase($this->getDatabase())
            ->get(1, 2, 3);

        //THEN
        $expected = [
            '_id' => [
                'assembly_id' => 1,
                'issue_id' => 2,
                'category' => 'a',
                'document_id' => 3,
            ],
            'document_id' => 3,
            'issue' => [
                '_id' => [
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'category' => 'a',
                ],
                'issue_id' => 2,
                'category' => 'a',
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
                'assembly' => null,
                'congressman' => null,
                'proponents' => [],
                'content_categories' => [],
                'content_super_categories' => [],
            ],
            'assembly' => [
                '_id' => 1,
                'assembly_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => null,
            ],
            'date' => '2001-01-01T00:00:00+00:00',
            'url' => 'url',
            'type' => 'type',
            'proponents' => [],
            'votes' => [],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testFetchByIssue()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Document::COLLECTION)->insertMany([
            (new DocumentPresenter)->serialize([
                'document_id' => 3,
                'issue' => [
                    'issue_id' => 2,
                    'assembly_id' => 1,
                    'category' => 'a'
                ],
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                ],
                'date' => '2001-01-01',
                'url' => 'url',
                'type' => 'type',
                'proponents' => [],
                'votes' => [],
            ]),
            (new DocumentPresenter)->serialize([
                'document_id' => 3,
                'issue' => [
                    'assembly_id' => 1,
                    'issue_id' => 3,
                    'category' => 'a'
                ],
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                ],
                'date' => '2001-01-01',
                'url' => 'url',
                'type' => 'type',
                'proponents' => [],
                'votes' => [],
            ]),
        ]);

        //WHEN
        $actual = (new Document())
            ->setSourceDatabase($this->getDatabase())
            ->fetchByIssue(1, 2);

        //THEN
        $expected = [[
            '_id' => [
                'assembly_id' => 1,
                'issue_id' => 2,
                'category' => 'a',
                'document_id' => 3,
            ],
            'document_id' => 3,
            'issue' => [
                '_id' => [
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'category' => 'a',
                ],
                'issue_id' => 2,
                'category' => 'a',
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
                'assembly' => null,
                'congressman' => null,
                'proponents' => [],
                'content_categories' => [],
                'content_super_categories' => [],
            ],
            'assembly' => [
                '_id' => 1,
                'assembly_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => null,
            ],
            'date' => '2001-01-01T00:00:00+00:00',
            'url' => 'url',
            'type' => 'type',
            'proponents' => [],
            'votes' => [],
        ]];

        $this->assertEquals($expected, $actual);
    }

    public function testStore()
    {
        //GIVEN

        //WHEN
        $result = (new Document())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'document_id' => 1,
                'issue' => [
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'congressman_id' => 1,
                    'category' => 'a',
                ],
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                ],
                'date' =>  '2001-01-01',
                'url' => 'url',
                'type' => 'type',
                'proponents' => [],
                'votes' => [],
            ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => new BSONDocument([
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'category' => 'a',
                    'document_id' => 1,
                ]),
                'document_id' => 1,
                'issue' => new BSONDocument([
                    '_id' => new BSONDocument([
                        'issue_id' => 1,
                        'assembly_id' => 1,
                        'category' => 'a',
                    ]),
                    'issue_id' => 1,
                    'category' => 'a',
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
                    'assembly' => null,
                    'congressman' => null,
                    'proponents' => new BSONArray([]),
                    'content_categories' => new BSONArray([]),
                    'content_super_categories' => new BSONArray([]),
                ]),
                'assembly' => new BSONDocument([
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => null,
                ]),
                'date' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'url' => 'url',
                'type' => 'type',
            ])
        ];
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Document::COLLECTION)->find([]),
            false
        );
        $createdResultCode = 1;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreVoteFirst()
    {
        //GIVEN
        //  an incomplete document, only containing _id and votes is already
        //  present,
        //  make sure that, that is not overwritten.
        $this->getDatabase()->selectCollection(Document::COLLECTION)->insertOne([
            '_id' => [
                'assembly_id' => 1,
                'issue_id' => 1,
                'category' => 'a',
                'document_id' => 1,
            ],
            'votes' => [(new DocumentOutcomePresenter)->serialize([
                'vote_id' => 1,
                'issue' => [
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'congressman_id' => 1,
                    'category' => 'a',
                ],
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                ],
                'document_id' => 1,
                'date' => '2001-01-01',
                'type' => 'type',
                'outcome' => 'outcome',
                'method' => 'method',
                'yes' => 1,
                'no' => 1,
                'inaction' => 1,
                'items' => [],
                'committee' => null,
                'committee_first_assembly' => null,
                'committee_last_assembly' => null,
            ])]
        ]);

        //WHEN
        $result = (new Document())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'document_id' => 1,
                'issue' => [
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'category' => 'a',
                ],
                'assembly' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                ],
                'date' =>  '2001-01-01',
                'url' => 'url',
                'type' => 'type',
                'proponents' => [],
                'votes' => [],
            ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => new BSONDocument([
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'category' => 'a',
                    'document_id' => 1,
                ]),
                'document_id' => 1,
                'issue' => new BSONDocument([
                    '_id' => new BSONDocument([
                        'issue_id' => 1,
                        'assembly_id' => 1,
                        'category' => 'a',
                    ]),
                    'issue_id' => 1,
                    'category' => 'a',
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
                    'assembly' => null,
                    'congressman' => null,
                    'proponents' => new BSONArray([]),
                    'content_categories' => new BSONArray([]),
                    'content_super_categories' => new BSONArray([]),
                ]),
                'assembly' => new BSONDocument([
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => null,
                ]),
                'date' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'url' => 'url',
                'type' => 'type',
                'votes' => new BSONArray([
                    new BSONDocument([
                        '_id' => new BSONDocument([
                            'assembly_id' => 1,
                            'issue_id' => 1,
                            'category' => 'a',
                            'document_id' => 1,
                            'vote_id' => 1,
                        ]),
                        'vote_id' => 1,
                        'issue' => new BSONDocument([
                            '_id' => new BSONDocument([
                                'assembly_id' => 1,
                                'issue_id' => 1,
                                'category' => 'a',
                            ]),
                            'category' => 'a',
                            'issue_id' => 1,
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
                            'assembly' => null,
                            'congressman' => null,
                            'proponents' => new BSONArray([]),
                            'content_categories' => new BSONArray([]),
                            'content_super_categories' => new BSONArray([]),
                        ]),
                        'assembly' => new BSONDocument([
                            '_id' => 1,
                            'assembly_id' => 1,
                            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                            'to' => null,
                        ]),
                        'date' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                        'type' => 'type',
                        'outcome' => 'outcome',
                        'method' => 'method',
                        'yes' => 1,
                        'no' => 1,
                        'inaction' => 1,
                        'items' => new BSONArray([]),
                        'committee' => null,
                        'committee_first_assembly' => null,
                        'committee_last_assembly' => null,
                    ])
                ]),
            ])
        ];
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Document::COLLECTION)->find([]),
            false
        );
        $createdResultCode = 2;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }
}
