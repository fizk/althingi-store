<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\CommitteeSitting;
use App\DatabaseConnectionTrait;
use DateTime;

class CommitteeSittingTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreSimpleStructureCreate()
    {
        //GIVEN
        // ...

        //WHEN
        $result = (new CommitteeSitting())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => '2001-01-01',
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'committee' => [
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => '2001-01-01',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => [
                    'party_id' => 3,
                    'name' => 'string',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => [
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'first_committee_assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'last_committee_assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
        ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'committee' => new BSONDocument([
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ]),
                'congressman' => new BSONDocument([
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ]),
                'congressman_party' => new BSONDocument([
                    'party_id' => 3,
                    'name' => 'string',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ]),
                'congressman_constituency' => new BSONDocument([
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ]),
                'first_committee_assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'last_committee_assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
            ])
        ];
        $createdResultCode = 1;
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->find([]),
            false
        );

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testGet()
    {
        //GIVE
        $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => [
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => [
                    'party_id' => 3,
                    'name' => 'string',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => [
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'first_committee_assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_committee_assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ],
            [
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => null,
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]
        ]);

        //WHEN
        $actual = (new CommitteeSitting())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        //THEN
        $expected = [
            '_id' => 1,
            'committee_sitting_id' => 1,
            'order' => 2,
            'role' => 'role',
            'from' => '2001-01-01T00:00:00+00:00',
            'to' => null,
            'assembly' => [
                'assembly_id' =>  2,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
            'committee' => [
                'committee_id' => 2,
                'name' => 'string',
                'first_assembly_id' => 1,
                'last_assembly_id' => 1,
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
            ],
            'congressman' => [
                'congressman_id' => 3,
                'name' => 'name',
                'birth' => '2001-01-01T00:00:00+00:00',
                'death' => null,
                'abbreviation' => 'abbreviation',
            ],
            'congressman_party' => [
                'party_id' => 3,
                'name' => 'string',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'color' => 'color',
            ],
            'congressman_constituency' => [
                'constituency_id' => 4,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
            ],
            'first_committee_assembly' => [
                'assembly_id' =>  2,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
            'last_committee_assembly' => [
                'assembly_id' =>  2,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testGetNotFound()
    {
        //GIVE
        $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => [
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => [
                    'party_id' => 3,
                    'name' => 'string',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => [
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'first_committee_assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_committee_assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ],
            [
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => null,
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]
        ]);

        //WHEN
        $actual = (new CommitteeSitting())
            ->setSourceDatabase($this->getDatabase())
            ->get(100);

        //THEN
        $expected = null;
        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => [
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => [
                    'party_id' => 3,
                    'name' => 'string',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => [
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'first_committee_assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_committee_assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ],
            [
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => null,
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]
        ]);

        //WHEN
        $actual = (new CommitteeSitting())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $expected = [
            [
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'committee' => [
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => '2001-01-01T00:00:00+00:00',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => [
                    'party_id' => 3,
                    'name' => 'string',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => [
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'first_committee_assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'last_committee_assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
            ],
            [
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'committee' => null,
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testFetchByAssembly()
    {
        $this->markTestSkipped('The order is never correct');

        //GIVE
        $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->insertMany([
            [
                '_id'  =>  1,
                'committee_sitting_id'  =>  1,
                'order'  =>  4,
                'role'  =>  'nefndarma??ur',
                'from'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'assembly'  =>  [
                    'assembly_id'  =>  74,
                    'from'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee'  =>  [
                    'committee_id'  =>  151,
                    'name'  =>  'allsherjarnefnd',
                    'first_assembly_id'  =>  27,
                    'last_assembly_id'  =>  139,
                    'abbr_long'  =>  'allshn.',
                    'abbr_short'  =>  'a'
                ],
                'congressman'  =>  [
                    'congressman_id'  =>  363,
                    'name'  =>  'J??rundur Brynj??lfsson',
                    'birth'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death'  =>  null,
                    'abbreviation'  =>  null
                ],
                'congressman_constituency'  =>  [
                    'constituency_id'  =>  40,
                    'name'  =>  '??rness??sla',
                    'abbr_short'  =>  '??r',
                    'abbr_long'  =>  '??rn.',
                    'description'  =>  null
                ],
                'congressman_party'  =>  [
                    'party_id'  =>  2,
                    'name'  =>  'Frams??knarflokkur',
                    'abbr_short'  =>  'F',
                    'abbr_long'  =>  'Framsfl.',
                    'color'  =>  null
                ],
                'first_committee_assembly'  =>  [
                    'assembly_id'  =>  27,
                    'from'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_committee_assembly'  =>  [
                    'assembly_id'  =>  139,
                    'from'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ],
            [
                '_id'  =>  2,
                'committee_sitting_id'  =>  2,
                'order'  =>  4,
                'role'  =>  'nefndarma??ur',
                'from'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'assembly'  =>  [
                    'assembly_id'  =>  74,
                    'from'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee'  =>  [
                    'committee_id'  =>  151,
                    'name'  =>  'allsherjarnefnd',
                    'first_assembly_id'  =>  27,
                    'last_assembly_id'  =>  139,
                    'abbr_long'  =>  'allshn.',
                    'abbr_short'  =>  'a'
                ],
                'congressman'  =>  [
                    'congressman_id'  =>  363,
                    'name'  =>  'J??rundur Brynj??lfsson',
                    'birth'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death'  =>  null,
                    'abbreviation'  =>  null
                ],
                'congressman_constituency'  =>  [
                    'constituency_id'  =>  40,
                    'name'  =>  '??rness??sla',
                    'abbr_short'  =>  '??r',
                    'abbr_long'  =>  '??rn.',
                    'description'  =>  null
                ],
                'congressman_party'  =>  [
                    'party_id'  =>  2,
                    'name'  =>  'Frams??knarflokkur',
                    'abbr_short'  =>  'F',
                    'abbr_long'  =>  'Framsfl.',
                    'color'  =>  null
                ],
                'first_committee_assembly'  =>  [
                    'assembly_id'  =>  27,
                    'from'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_committee_assembly'  =>  [
                    'assembly_id'  =>  139,
                    'from'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ],
            [
                '_id'  =>  3,
                'committee_sitting_id'  =>  3,
                'order'  =>  4,
                'role'  =>  'nefndarma??ur',
                'from'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'assembly'  =>  [
                    'assembly_id'  =>  74,
                    'from'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee'  =>  [
                    'committee_id'  =>  151,
                    'name'  =>  'allsherjarnefnd',
                    'first_assembly_id'  =>  27,
                    'last_assembly_id'  =>  139,
                    'abbr_long'  =>  'allshn.',
                    'abbr_short'  =>  'a'
                ],
                'congressman'  =>  [
                    'congressman_id'  =>  2,
                    'name'  =>  'J??rundur Brynj??lfsson',
                    'birth'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death'  =>  null,
                    'abbreviation'  =>  null
                ],
                'congressman_constituency'  =>  [
                    'constituency_id'  =>  40,
                    'name'  =>  '??rness??sla',
                    'abbr_short'  =>  '??r',
                    'abbr_long'  =>  '??rn.',
                    'description'  =>  null
                ],
                'congressman_party'  =>  [
                    'party_id'  =>  2,
                    'name'  =>  'Frams??knarflokkur',
                    'abbr_short'  =>  'F',
                    'abbr_long'  =>  'Framsfl.',
                    'color'  =>  null
                ],
                'first_committee_assembly'  =>  [
                    'assembly_id'  =>  27,
                    'from'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_committee_assembly'  =>  [
                    'assembly_id'  =>  139,
                    'from'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to'  => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ]
        ]);

        //WHEN
        $actual = (new CommitteeSitting())
            ->setSourceDatabase($this->getDatabase())
            ->fetchByAssembly(74);

        $expected = [
            [
                'committee_id'  =>  151,
                'name'  =>  'allsherjarnefnd',
                'first_assembly_id'  =>  27,
                'last_assembly_id'  =>  139,
                'abbr_long'  =>  'allshn.',
                'abbr_short'  =>  'a',
                'assembly' => [
                    'assembly_id'  =>  74,
                    'from'  => '2001-01-01T00:00:00+00:00',
                    'to'  => '2001-01-01T00:00:00+00:00',
                ],
                'first_assembly' => [
                    'assembly_id'  =>  27,
                    'from'  => '2001-01-01T00:00:00+00:00',
                    'to'  => '2001-01-01T00:00:00+00:00',
                ],
                'last_assembly' => [
                    'assembly_id'  =>  139,
                    'from'  => '2001-01-01T00:00:00+00:00',
                    'to'  => '2001-01-01T00:00:00+00:00',
                ],
                'sessions' => [
                    [
                        '_id' => 1,
                        'congressman' => [
                            'congressman_id'  =>  363,
                            'name'  =>  'J??rundur Brynj??lfsson',
                            'birth'  => '2001-01-01T00:00:00+00:00',
                            'death'  =>  null,
                            'abbreviation'  =>  null
                        ],
                        'assembly' => [
                            'assembly_id'  =>  74,
                            'from'  => '2001-01-01T00:00:00+00:00',
                            'to'  => '2001-01-01T00:00:00+00:00',
                        ],
                        'sessions' => [
                            [
                                '_id'  =>  1,
                                'order'  =>  4,
                                'type'  =>  'nefndarma??ur',
                                'from'  => '2001-01-01T00:00:00+00:00',
                                'to'  => '2001-01-01T00:00:00+00:00',
                                'abbr' => null,
                                'assembly' => [
                                    'assembly_id'  =>  74,
                                    'from'  => '2001-01-01T00:00:00+00:00',
                                    'to'  => '2001-01-01T00:00:00+00:00',
                                ],
                                'congressman_constituency'  =>  [
                                    'constituency_id'  =>  40,
                                    'name'  =>  '??rness??sla',
                                    'abbr_short'  =>  '??r',
                                    'abbr_long'  =>  '??rn.',
                                    'description'  =>  null
                                ],
                                'congressman_party'  =>  [
                                    'party_id'  =>  2,
                                    'name'  =>  'Frams??knarflokkur',
                                    'abbr_short'  =>  'F',
                                    'abbr_long'  =>  'Framsfl.',
                                    'color'  =>  null
                                ],
                            ],
                            [
                                '_id'  =>  2,
                                'committee_sitting_id'  =>  2,
                                'order'  =>  4,
                                'type'  =>  'nefndarma??ur',
                                'from'  => '2001-01-01T00:00:00+00:00',
                                'to'  => '2001-01-01T00:00:00+00:00',
                                'abbr' => null,
                                'assembly' => [
                                    'assembly_id'  =>  74,
                                    'from'  => '2001-01-01T00:00:00+00:00',
                                    'to'  => '2001-01-01T00:00:00+00:00',
                                ],
                                'congressman_constituency'  =>  [
                                    'constituency_id'  =>  40,
                                    'name'  =>  '??rness??sla',
                                    'abbr_short'  =>  '??r',
                                    'abbr_long'  =>  '??rn.',
                                    'description'  =>  null
                                ],
                                'congressman_party'  =>  [
                                    'party_id'  =>  2,
                                    'name'  =>  'Frams??knarflokkur',
                                    'abbr_short'  =>  'F',
                                    'abbr_long'  =>  'Framsfl.',
                                    'color'  =>  null
                                ],
                            ]
                        ]
                    ],
                    [
                        '_id'  =>  3,
                        'congressman'  =>  [
                            'congressman_id'  =>  2,
                            'name'  =>  'J??rundur Brynj??lfsson',
                            'birth'  => '2001-01-01T00:00:00+00:00',
                            'death'  =>  null,
                            'abbreviation'  =>  null
                        ],
                        'assembly' => [
                            'assembly_id'  =>  74,
                            'from'  => '2001-01-01T00:00:00+00:00',
                            'to'  => '2001-01-01T00:00:00+00:00',
                        ],
                        'sessions' => [
                            [
                                '_id'  =>  3,
                                'committee_sitting_id'  =>  3,
                                'order'  =>  4,
                                'type'  =>  'nefndarma??ur',
                                'from'  => '2001-01-01T00:00:00+00:00',
                                'to'  => '2001-01-01T00:00:00+00:00',
                                'abbr' => null,
                                'assembly' => [
                                    'assembly_id'  =>  74,
                                    'from'  => '2001-01-01T00:00:00+00:00',
                                    'to'  => '2001-01-01T00:00:00+00:00',
                                ],
                                'congressman_constituency'  =>  [
                                    'constituency_id'  =>  40,
                                    'name'  =>  '??rness??sla',
                                    'abbr_short'  =>  '??r',
                                    'abbr_long'  =>  '??rn.',
                                    'description'  =>  null
                                ],
                                'congressman_party'  =>  [
                                    'party_id'  =>  2,
                                    'name'  =>  'Frams??knarflokkur',
                                    'abbr_short'  =>  'F',
                                    'abbr_long'  =>  'Framsfl.',
                                    'color'  =>  null
                                ],
                            ]
                        ],
                    ]
                ],
            ]
        ];

        $i = 0;
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateAssembly()
    {
        //GIVE
        $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => [
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => [
                    'party_id' => 3,
                    'name' => 'string',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => [
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'first_committee_assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'last_committee_assembly' => [
                    'assembly_id' =>  3,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
            ],
            [
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => null,
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]
        ]);

        //WHEN
        (new CommitteeSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateAssembly([
                'assembly_id' =>  2,
                'from' => '1978-01-01',
                'to' => '1978-01-01',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->find([]),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                ]),
                'committee' => new BSONDocument([
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ]),
                'congressman' => new BSONDocument([
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ]),
                'congressman_party' => new BSONDocument([
                    'party_id' => 3,
                    'name' => 'string',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ]),
                'congressman_constituency' => new BSONDocument([
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ]),
                'first_committee_assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                ]),
                'last_committee_assembly' => new BSONDocument([
                    'assembly_id' =>  3,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
            ]),
            new BSONDocument([
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                ]),
                'committee' => null,
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ])
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateCommittee()
    {
        //GIVE
        $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => [
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ],
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ],
            [
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => [
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ],
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]
        ]);

        //WHEN
        (new CommitteeSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateCommittee([
                'committee_id' => 2,
                'name' => 'string-edit',
                'first_assembly_id' => 3,
                'last_assembly_id' => 3,
                'abbr_long' => 'abbr_long-edit',
                'abbr_short' => 'abbr_short-edit',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->find([]),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'committee' => new BSONDocument([
                    'committee_id' => 2,
                    'name' => 'string-edit',
                    'first_assembly_id' => 3,
                    'last_assembly_id' => 3,
                    'abbr_long' => 'abbr_long-edit',
                    'abbr_short' => 'abbr_short-edit',
                ]),
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]),
            new BSONDocument([
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'committee' => new BSONDocument([
                    'committee_id' => 2,
                    'name' => 'string-edit',
                    'first_assembly_id' => 3,
                    'last_assembly_id' => 3,
                    'abbr_long' => 'abbr_long-edit',
                    'abbr_short' => 'abbr_short-edit',
                ]),
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ])
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateCommitteeNoUpdate()
    {
        //GIVE
        $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => [
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ],
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ],
            [
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => [
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ],
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]
        ]);

        //WHEN
        (new CommitteeSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateCommittee([
                'committee_id' => 20,
                'name' => 'string-edit',
                'first_assembly_id' => 3,
                'last_assembly_id' => 3,
                'abbr_long' => 'abbr_long-edit',
                'abbr_short' => 'abbr_short-edit',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->find([]),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'committee' => new BSONDocument([
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ]),
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]),
            new BSONDocument([
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'committee' => new BSONDocument([
                    'committee_id' => 2,
                    'name' => 'string',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 1,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short',
                ]),
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ])
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateCongressman()
    {
        //GIVE
        $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => null,
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ],
            [
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => null,
                'congressman' => [
                    'congressman_id' => 2,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]
        ]);

        //WHEN
        (new CommitteeSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateCongressman([
                'congressman_id' => 3,
                'name' => 'name',
                'birth' => '1978-04-11',
                'death' => null,
                'abbreviation' => 'abbreviation',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->find([]),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'committee' => null,
                'congressman' => new BSONDocument([
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('1978-04-11'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ]),
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]),
            new BSONDocument([
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'committee' => null,
                'congressman' => new BSONDocument([
                    'congressman_id' => 2,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ]),
                'congressman_party' => null,
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ])
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateParty()
    {
        //GIVE
        $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => null,
                'congressman' => null,
                'congressman_party' => [
                    'party_id' => 3,
                    'name' => 'string',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ],
            [
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'committee' => null,
                'congressman' => null,
                'congressman_party' => [
                    'party_id' => 1,
                    'name' => 'string',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]
        ]);

        //WHEN
        (new CommitteeSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateParty([
                'party_id' => 1,
                'name' => 'string-edit',
                'abbr_short' => 'abbr_short-edit',
                'abbr_long' => 'abbr_long-edit',
                'color' => 'color-edit',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->find([]),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'committee' => null,
                'congressman' => null,
                'congressman_party' => new BSONDocument([
                    'party_id' => 3,
                    'name' => 'string',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ]),
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]),
            new BSONDocument([
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'committee' => null,
                'congressman' => null,
                'congressman_party' => new BSONDocument([
                    'party_id' => 1,
                    'name' => 'string-edit',
                    'abbr_short' => 'abbr_short-edit',
                    'abbr_long' => 'abbr_long-edit',
                    'color' => 'color-edit',
                ]),
                'congressman_constituency' => null,
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ])
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateConstituency()
    {
        //GIVE
        $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->insertMany([
            [
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => null,
                    'to' => null,
                ],
                'committee' => null,
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => [
                    'constituency_id' => 3,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ],
            [
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => null,
                    'to' => null,
                ],
                'committee' => null,
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => [
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]
        ]);

        //WHEN
        (new CommitteeSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateConstituency([
                'constituency_id' => 3,
                'name' => 'name-edit',
                'abbr_short' => 'name-edit',
                'abbr_long' => 'name-edit',
                'description' => 'name-edit',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(CommitteeSitting::COLLECTION)->find([]),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'committee_sitting_id' => 1,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => null,
                    'to' => null,
                ]),
                'committee' => null,
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => new BSONDocument([
                    'constituency_id' => 3,
                    'name' => 'name-edit',
                    'abbr_short' => 'name-edit',
                    'abbr_long' => 'name-edit',
                    'description' => 'name-edit',
                ]),
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]),
            new BSONDocument([
                '_id' => 2,
                'committee_sitting_id' => 2,
                'order' => 2,
                'role' => 'role',
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => null,
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => null,
                    'to' => null,
                ]),
                'committee' => null,
                'congressman' => null,
                'congressman_party' => null,
                'congressman_constituency' => new BSONDocument([
                    'constituency_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ]),
                'first_committee_assembly' => null,
                'last_committee_assembly' => null,
            ]),
        ];
        $this->assertEquals($expected, $actual);
    }
}
