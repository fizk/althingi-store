<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\TestCase;
use App\Service\PresidentSitting;
use App\DatabaseConnectionTrait;
use DateTime;
use MongoDB\BSON\UTCDateTime;

class PresidentSittingTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreCreate()
    {
        //GIVEN
        // ...

        //WHEN
        $result = (new PresidentSitting())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'president_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                'title' => 'title',
                'abbr' => 'abbr',
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' =>  '2001-01-01',
                    'to' =>  '2001-01-01',
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => '2001-01-01',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => [
                    'party_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => [
                    'constituency_id' => 5,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
        ]);

        //THEN
        $actual = iterator_to_array($this->getDatabase()->selectCollection(
            PresidentSitting::COLLECTION)->find([]),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'president_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'title' => 'title',
                'abbr' => 'abbr',
                'assembly' => new BSONDocument([
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'congressman' => new BSONDocument([
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ]),
                'congressman_party' => new BSONDocument([
                    'party_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ]),
                'congressman_constituency' => new BSONDocument([
                    'constituency_id' => 5,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
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
        $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->insertOne([
            '_id' => 1,
            'president_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'title' => 'title',
            'abbr' => 'abbr',
            'assembly' => [
                'assembly_id' =>  2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ],
            'congressman' => [
                'congressman_id' => 3,
                'name' => 'name',
                'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'death' => null,
                'abbreviation' => 'abbreviation',
            ],
            'congressman_party' => [
                'party_id' => 4,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'color' => 'color',
            ],
            'congressman_constituency' => [
                'constituency_id' => 5,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
            ],
        ]);

        //WHEN
        $actual = (new PresidentSitting())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        //THEN
        $expected =
            [
                '_id' => 1,
                'president_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
                'title' => 'title',
                'abbr' => 'abbr',
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => '2001-01-01T00:00:00+00:00',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => [
                    'party_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => [
                    'constituency_id' => 5,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
            ]
        ;
        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->insertOne([
            '_id' => 1,
            'president_id' => 1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'title' => 'title',
            'abbr' => 'abbr',
            'assembly' => [
                'assembly_id' =>  2,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ],
            'congressman' => [
                'congressman_id' => 3,
                'name' => 'name',
                'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'death' => null,
                'abbreviation' => 'abbreviation',
            ],
            'congressman_party' => [
                'party_id' => 4,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'color' => 'color',
            ],
            'congressman_constituency' => [
                'constituency_id' => 5,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
            ],
        ]);

        //WHEN
        $actual = (new PresidentSitting())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $expected =[
            [
                '_id' => 1,
                'president_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
                'title' => 'title',
                'abbr' => 'abbr',
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => '2001-01-01T00:00:00+00:00',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => [
                    'party_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => [
                    'constituency_id' => 5,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
            ]
        ];
        $this->assertEquals($expected, $actual);
    }

}
