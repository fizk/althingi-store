<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONArray;
use PHPUnit\Framework\TestCase;
use App\Service\Issue;
use App\DatabaseConnectionTrait;
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
                    'assembly_id' => 140,
                    'from' => new UTCDateTime((new DateTime('2011-10-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2012-09-10'))->getTimestamp() * 1000),
                ]),
                'congressman' => null
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
            [
                '_id' => [
                    'assembly_id' => 140,
                    'issue_id' => 1,
                    'category' => 'a'
                ],
                'additional_information' => null,
                'assembly' => [
                    'assembly_id' => '140',
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
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
                'proponents' => new BSONArray([]),
                'question' => null,
                'status' => 'Samþykkt sem lög frá Alþingi',
                'sub_name' => null,
                'type' => 'l',
                'type_name' => 'Frumvarp til laga',
                'type_subname' => 'lagafrumvarp'
            ]
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
                'assembly_id' => '140',
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
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
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testFetchByAssembly()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Issue::COLLECTION)->insertMany([
            [
                '_id' => [
                    'assembly_id' => 140,
                    'issue_id' => 1,
                    'category' => 'a'
                ],
                'additional_information' => null,
                'assembly' => [
                    'assembly_id' => 140,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
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
                'proponents' => new BSONArray([]),
                'question' => null,
                'status' => 'Samþykkt sem lög frá Alþingi',
                'sub_name' => null,
                'type' => 'l',
                'type_name' => 'Frumvarp til laga',
                'type_subname' => 'lagafrumvarp'
            ],
            [
                '_id' => [
                    'assembly_id' => 141,
                    'issue_id' => 1,
                    'category' => 'a'
                ],
                'additional_information' => null,
                'assembly' => [
                    'assembly_id' => 141,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
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
                'proponents' => new BSONArray([]),
                'question' => null,
                'status' => 'Samþykkt sem lög frá Alþingi',
                'sub_name' => null,
                'type' => 'l',
                'type_name' => 'Frumvarp til laga',
                'type_subname' => 'lagafrumvarp'
            ],
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
                    'assembly_id' => '140',
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
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
            ]
        ];
        $this->assertEquals($expected, $actual);
    }
}
