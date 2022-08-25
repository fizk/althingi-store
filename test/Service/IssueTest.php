<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONArray;
use PHPUnit\Framework\TestCase;
use App\Service\Issue;
use App\DatabaseConnectionTrait;
use App\Presenter\IssuePresenter;
use DateTime;

class IssueTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStore()
    {
        //GIVEN

        //WHEN
        $result = (new Issue())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'issue_id'=> 1168,
                'category'=> "b",
                'name'=> "aðgerðir gegn einelti",
                'sub_name'=> null,
                'type'=> "ft",
                'type_name'=> "óundirbúinn fyrirspurnatími",
                'type_subname'=> null,
                'status'=> null,
                'question'=> null,
                'goal'=> null,
                'major_changes'=> null,
                'changes_in_law'=> null,
                'costs_and_revenues'=> null,
                'deliveries'=> null,
                'additional_information'=> null,
                'proponents'=> [],
                'assembly'=>  [
                    'assembly_id'=> 140,
                    'from'=> "2011-10-01",
                    'to'=> "2012-09-10"
                ],
                'congressman'=> null
        ]);

        //THEN
        $createdResultCode = 1;
        $expected = [
            new BSONDocument([
                '_id' => new BSONDocument([
                    'assembly_id' => 140,
                    'issue_id' => 1168,
                    'category' => "b",
                ]),
                'issue_id' => 1168,
                'category' => "b",
                'name' => "aðgerðir gegn einelti",
                'sub_name' => null,
                'type' => "ft",
                'type_name' => "óundirbúinn fyrirspurnatími",
                'type_subname' => null,
                'status' => null,
                'question' => null,
                'goal' => null,
                'major_changes' => null,
                'changes_in_law' => null,
                'costs_and_revenues' => null,
                'deliveries' => null,
                'additional_information' => null,
                'proponents' => new BSONArray([]),
                'assembly' =>  new BSONDocument([
                    '_id' => 140,
                    'assembly_id' => 140,
                    'from' => new UTCDateTime((new DateTime('2011-10-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2012-09-10'))->getTimestamp() * 1000),
                ]),
                'congressman' => null,
                'content_categories' => new BSONArray([]),
                'content_super_categories' => new BSONArray([]),
            ])
        ];
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Issue::COLLECTION)->find([]),
            false
        );
        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testGet()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Issue::COLLECTION)->insertMany([
            (new IssuePresenter)->serialize([
                'additional_information' => null,
                'assembly' => [
                    'assembly_id' => '140',
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'category' => 'a',
                'changes_in_law' => null,
                'congressman' => null,
                'costs_and_revenues' => null,
                'deliveries' => null,
                'goal' => null,
                'issue_id' => 1,
                'major_changes' => null,
                'name' => 'fjárlög 2012',
                'proponents' => [],
                'question' => null,
                'status' => 'Samþykkt sem lög frá Alþingi',
                'sub_name' => null,
                'type' => 'l',
                'type_name' => 'Frumvarp til laga',
                'type_subname' => 'lagafrumvarp'
            ])
        ]);

        //WHEN
        $actual = (new Issue())
            ->setSourceDatabase($this->getDatabase())
            ->get(140, 1, 'a');

        // THEN
        $expected = [
            '_id' => [
                'assembly_id' => 140,
                'issue_id' => 1,
                'category' => 'a'
            ],
            'additional_information' => null,
            'assembly' => [
                '_id' => 140,
                'assembly_id' => 140,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
            'category' => 'a',
            'changes_in_law' => null,
            'congressman' => null,
            'costs_and_revenues' => null,
            'deliveries' => null,
            'goal' => null,
            'issue_id' => 1,
            'major_changes' => null,
            'name' => 'fjárlög 2012',
            'proponents' => [],
            'question' => null,
            'status' => 'Samþykkt sem lög frá Alþingi',
            'sub_name' => null,
            'type' => 'l',
            'type_name' => 'Frumvarp til laga',
            'type_subname' => 'lagafrumvarp',
            'content_categories' => [],
            'content_super_categories' => [],
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testFetchByAssembly()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Issue::COLLECTION)->insertMany([
            (new IssuePresenter)->serialize([
                'additional_information' => null,
                'assembly' => [
                    'assembly_id' => 140,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'category' => 'a',
                'changes_in_law' => null,
                'congressman' => null,
                'costs_and_revenues' => null,
                'deliveries' => null,
                'goal' => null,
                'issue_id' => '1',
                'major_changes' => null,
                'name' => 'fjárlög 2012',
                'proponents' => [],
                'question' => null,
                'status' => 'Samþykkt sem lög frá Alþingi',
                'sub_name' => null,
                'type' => 'l',
                'type_name' => 'Frumvarp til laga',
                'type_subname' => 'lagafrumvarp'
            ]),
            (new IssuePresenter)->serialize([
                'additional_information' => null,
                'assembly' => [
                    'assembly_id' => 141,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'category' => 'a',
                'changes_in_law' => null,
                'congressman' => null,
                'costs_and_revenues' => null,
                'deliveries' => null,
                'goal' => null,
                'issue_id' => '1',
                'major_changes' => null,
                'name' => 'fjárlög 2012',
                'proponents' => [],
                'question' => null,
                'status' => 'Samþykkt sem lög frá Alþingi',
                'sub_name' => null,
                'type' => 'l',
                'type_name' => 'Frumvarp til laga',
                'type_subname' => 'lagafrumvarp'
            ]),
        ]);

        //WHEN
        $actual = (new Issue())
            ->setSourceDatabase($this->getDatabase())
            ->fetchByAssembly(140);

        // THEN
        $expected = [
            [
                '_id' => [
                    'assembly_id' => 140,
                    'issue_id' => 1,
                    'category' => 'a'
                ],
                'additional_information' => null,
                'assembly' => [
                    '_id' => 140,
                    'assembly_id' => 140,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'category' => 'a',
                'changes_in_law' => null,
                'congressman' => null,
                'costs_and_revenues' => null,
                'deliveries' => null,
                'goal' => null,
                'issue_id' => 1,
                'major_changes' => null,
                'name' => 'fjárlög 2012',
                'proponents' => [],
                'question' => null,
                'status' => 'Samþykkt sem lög frá Alþingi',
                'sub_name' => null,
                'type' => 'l',
                'type_name' => 'Frumvarp til laga',
                'type_subname' => 'lagafrumvarp',
                'content_categories' => [],
                'content_super_categories' => [],
            ]
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testAddContentCategory()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Issue::COLLECTION)->insertMany([
            (new IssuePresenter)->serialize([
                'assembly' => [
                    'assembly_id' => 140,
                ],
                'issue_id' => 1,
                'category' => 'a',
                'content_categories' => [],
            ]),
            (new IssuePresenter)->serialize([
                'assembly' => [
                    'assembly_id' => 140,
                ],
                'issue_id' => 2,
                'category' => 'a',
                'content_categories' => [],
            ]),
        ]);

        $result = (new Issue())
            ->setSourceDatabase($this->getDatabase())
            ->addContentCategory(140, 1, 'a', [
                'category_id' => 1,
                'super_category_id' => 2,
                'title' => 'Maybe<string>',
                'description' => 'Maybe<string>',
            ]);

        // THEN
        $expectedIssue1 = new BSONArray(
            [
                new BSONDocument([
                    '_id' => 1,
                    'category_id' => 1,
                    'super_category_id' => 2,
                    'title' => 'Maybe<string>',
                    'description' => 'Maybe<string>',
                ])
            ]
        );
        $expectedIssue2 = new BSONArray([]);
        $createdResultCode = 2;
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Issue::COLLECTION)->find([]),
            false
        );
        $this->assertEquals($expectedIssue1, $actual[0]['content_categories']);
        $this->assertEquals($expectedIssue2, $actual[1]['content_categories']);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testAddContentCategoryOnlyUpdatesOnceIfTheIssueAlreadyHasThisCategory()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Issue::COLLECTION)->insertMany([
            (new IssuePresenter)->serialize([
                'assembly' => [
                    'assembly_id' => 140,
                ],
                'issue_id' => 1,
                'category' => 'a',
                'content_categories' => [],
            ]),
        ]);

        //WHEN
        $firstResponse = (new Issue())
            ->setSourceDatabase($this->getDatabase())
            ->addContentCategory(140, 1, 'a', [
                'category_id' => 1,
                'super_category_id' => 2,
                'title' => 'Maybe<string>',
                'description' => 'Maybe<string>',
            ]);
        (new Issue())
            ->setSourceDatabase($this->getDatabase())
            ->addContentCategory(140, 1, 'a', [
                'category_id' => 1,
                'super_category_id' => 2,
                'title' => 'Maybe<string>',
                'description' => 'Maybe<string>',
            ]);
        $lastResponse = (new Issue())
            ->setSourceDatabase($this->getDatabase())
            ->addContentCategory(140, 1, 'a', [
                'category_id' => 1,
                'super_category_id' => 2,
                'title' => 'Maybe<string>',
                'description' => 'Maybe<string>',
            ]);

        // THEN
        $expected = new BSONArray(
            [
                new BSONDocument([
                    '_id' => 1,
                    'category_id' => 1,
                    'super_category_id' => 2,
                    'title' => 'Maybe<string>',
                    'description' => 'Maybe<string>',
                ])
            ]
        );
        $firstResponseCode = 2;
        $lastResponseCode = 0;
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Issue::COLLECTION)->find([]),
            false
        );
        $this->assertEquals($expected, $actual[0]['content_categories']);
        $this->assertEquals($firstResponse, $firstResponseCode);
        $this->assertEquals($lastResponse, $lastResponseCode);
    }

    public function testAddSuperContentCategory()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Issue::COLLECTION)->insertMany([
            (new IssuePresenter)->serialize([
                'assembly' => [
                    'assembly_id' => 140,
                ],
                'issue_id' => 1,
                'category' => 'a',
                'content_super_categories' => [],
            ]),
            (new IssuePresenter)->serialize([
                'assembly' => [
                    'assembly_id' => 140,
                ],
                'issue_id' => 2,
                'category' => 'a',
                'content_super_categories' => [],
            ]),
        ]);

        $result = (new Issue())
            ->setSourceDatabase($this->getDatabase())
            ->addContentSuperCategory(140, 1, 'a', [
                'super_category_id' => 2,
                'title' => 'Maybe<string>',
            ]);

        // THEN
        $expectedIssue1 = new BSONArray(
            [
                new BSONDocument([
                    '_id' => 2,
                    'super_category_id' => 2,
                    'title' => 'Maybe<string>',
                ])
            ]
        );
        $expectedIssue2 = new BSONArray([]);
        $createdResultCode = 2;
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Issue::COLLECTION)->find([]),
            false
        );
        $this->assertEquals($expectedIssue1, $actual[0]['content_super_categories']);
        $this->assertEquals($expectedIssue2, $actual[1]['content_super_categories']);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testAddSuperContentCategoryOnlyUpdatesOnceIfTheIssueAlreadyHasThisCategory()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Issue::COLLECTION)->insertMany([
            (new IssuePresenter)->serialize([
                'assembly' => [
                    'assembly_id' => 140,
                ],
                'issue_id' => 1,
                'category' => 'a',
                'content_super_categories' => [],
            ]),
        ]);

        //WHEN
        $firstResponse = (new Issue())
            ->setSourceDatabase($this->getDatabase())
            ->addContentSuperCategory(140, 1, 'a', [
                'super_category_id' => 2,
                'title' => 'Maybe<string>',
            ]);
        (new Issue())
            ->setSourceDatabase($this->getDatabase())
            ->addContentSuperCategory(140, 1, 'a', [
                'super_category_id' => 2,
                'title' => 'Maybe<string>',
            ]);
        $lastResponse = (new Issue())
            ->setSourceDatabase($this->getDatabase())
            ->addContentSuperCategory(140, 1, 'a', [
                'super_category_id' => 2,
                'title' => 'Maybe<string>',
            ]);

        // THEN
        $expected = new BSONArray(
            [
                new BSONDocument([
                    '_id' => 2,
                    'super_category_id' => 2,
                    'title' => 'Maybe<string>',
                ])
            ]
        );
        $firstResponseCode = 2;
        $lastResponseCode = 0;
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Issue::COLLECTION)->find([]),
            false
        );
        $this->assertEquals($expected, $actual[0]['content_super_categories']);
        $this->assertEquals($firstResponse, $firstResponseCode);
        $this->assertEquals($lastResponse, $lastResponseCode);
    }
}
