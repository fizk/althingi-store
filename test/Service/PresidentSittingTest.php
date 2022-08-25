<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\TestCase;
use App\Service\PresidentSitting;
use App\DatabaseConnectionTrait;
use App\Presenter\PresidentSittingPresenter;
use MongoDB\BSON\UTCDateTime;
use DateTime;

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
                    '_id' =>  2,
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'congressman' => new BSONDocument([
                    '_id' => 3,
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ]),
                'congressman_party' => new BSONDocument([
                    '_id' => 4,
                    'party_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ]),
                'congressman_constituency' => new BSONDocument([
                    '_id' => 5,
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
        $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->insertOne(
            (new PresidentSittingPresenter)->serialize([
                'president_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                'title' => 'title',
                'abbr' => 'abbr',
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
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
            ])
        );

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
                    '_id' =>  2,
                    'assembly_id' =>  2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'congressman' => [
                    '_id' => 3,
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => '2001-01-01T00:00:00+00:00',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => [
                    '_id' => 4,
                    'party_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => [
                    '_id' => 5,
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
        $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->insertOne(
            (new PresidentSittingPresenter)->serialize([
                'president_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                'title' => 'title',
                'abbr' => 'abbr',
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
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
            ])
        );

        //WHEN
        $actual = (new PresidentSitting())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $expected = [
            [
                '_id' => 1,
                'president_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
                'title' => 'title',
                'abbr' => 'abbr',
                'assembly' => [
                    '_id' =>  2,
                    'assembly_id' =>  2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'congressman' => [
                    '_id' => 3,
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => '2001-01-01T00:00:00+00:00',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'congressman_party' => [
                    '_id' => 4,
                    'party_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
                'congressman_constituency' => [
                    '_id' => 5,
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

    public function testFetchByAssembly()
    {
        //GIVE
        $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->insertOne(
            (new PresidentSittingPresenter)->serialize([
                'president_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                'title' => 'title',
                'abbr' => 'abbr',
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
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
            ])
        );

        //WHEN
        $actual = (new PresidentSitting())
            ->setSourceDatabase($this->getDatabase())
            ->fetchByAssembly(2);

        //THEN
        $expected =[
            [
                '_id' => 1,
                'assembly' => [
                    '_id' =>  2,
                    'assembly_id' =>  2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'congressman' => [
                    '_id' => 3,
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => '2001-01-01T00:00:00+00:00',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
                'sessions' => [
                   [
                        '_id' => 1,
                        'from' => '2001-01-01T00:00:00+00:00',
                        'to' => '2001-01-01T00:00:00+00:00',
                        'type' => 'title',
                        'abbr' => 'abbr',
                        'congressman_party' => [
                            '_id' => 4,
                            'party_id' => 4,
                            'name' => 'name',
                            'abbr_short' => 'abbr_short',
                            'abbr_long' => 'abbr_long',
                            'color' => 'color',
                        ],
                        'congressman_constituency' => [
                            '_id' => 5,
                            'constituency_id' => 5,
                            'name' => 'name',
                            'abbr_short' => 'abbr_short',
                            'abbr_long' => 'abbr_long',
                            'description' => 'description',
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
        $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->insertMany([
            (new PresidentSittingPresenter)->serialize([
                'president_id' => 1,
                'assembly' => [
                    'assembly_id' =>  1,
                    'from' => null,
                    'to' => null,
                ],
            ]),
            (new PresidentSittingPresenter)->serialize([
                'president_id' => 2,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => null,
                    'to' => null,
                ],
            ]),
        ]);

        // GIVEN
        (new PresidentSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateAssembly([
                'assembly_id' =>  1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
            ]);

        //THEN
        $result = iterator_to_array(
            $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->find(),
            true
        );
        $expectedAssemblyOne = new BSONDocument([
            '_id' =>  1,
            'assembly_id' =>  1,
            'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
        ]);
        $expectedAssemblyTwo = new BSONDocument([
            '_id' =>  2,
            'assembly_id' =>  2,
            'from' => null,
            'to' => null,
        ]);
        $this->assertEquals($expectedAssemblyOne, $result[0]['assembly']);
        $this->assertEquals($expectedAssemblyTwo, $result[1]['assembly']);
    }

    public function testUpdateParty()
    {
        //GIVE
        $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->insertMany([
            (new PresidentSittingPresenter)->serialize([
                'president_id' => 1,
                'congressman_party' => [
                    'party_id' => 1,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
            ]),
            (new PresidentSittingPresenter)->serialize([
                'president_id' => 2,
                'congressman_party' => [
                    'party_id' => 4,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'color' => 'color',
                ],
            ]),
        ]);

        // GIVEN
        (new PresidentSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateParty([
                'party_id' => 1,
                'name' => 'update-name',
                'abbr_short' => 'update-abbr_short',
                'abbr_long' => 'update-abbr_long',
                'color' => 'update-color',
            ]);

        //THEN
        $result = iterator_to_array(
            $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->find(),
            true
        );
        $expectedPartyOne = new BSONDocument([
            '_id' => 1,
            'party_id' => 1,
            'name' => 'update-name',
            'abbr_short' => 'update-abbr_short',
            'abbr_long' => 'update-abbr_long',
            'color' => 'update-color',
        ]);
        $expectedPartyTwo = new BSONDocument([
            '_id' => 4,
            'party_id' => 4,
            'name' => 'name',
            'abbr_short' => 'abbr_short',
            'abbr_long' => 'abbr_long',
            'color' => 'color',
        ]);
        $this->assertEquals($expectedPartyOne, $result[0]['congressman_party']);
        $this->assertEquals($expectedPartyTwo, $result[1]['congressman_party']);
    }

    public function testUpdateCongressman()
    {
        //GIVE
        $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->insertMany([
            (new PresidentSittingPresenter)->serialize([
                'president_id' => 1,
                'congressman' => [
                    'congressman_id' => 1,
                    'name' => 'name',
                    'birth' => '2001-01-01',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
            ]),
            (new PresidentSittingPresenter)->serialize([
                'president_id' => 2,
                'congressman' => [
                    'congressman_id' => 3,
                    'name' => 'name',
                    'birth' => '2001-01-01',
                    'death' => null,
                    'abbreviation' => 'abbreviation',
                ],
            ]),
        ]);

        // GIVEN
        (new PresidentSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateCongressman([
                'congressman_id' => 1,
                'name' => 'update-name',
                'birth' => '2001-01-02',
                'death' => null,
                'abbreviation' => 'update-abbreviation',
            ]);

        //THEN
        $result = iterator_to_array(
            $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->find(),
            true
        );
        $expectedCongressmanOne = new BSONDocument([
            '_id' => 1,
            'congressman_id' => 1,
            'name' => 'update-name',
            'birth' => new UTCDateTime((new DateTime('2001-01-02'))->getTimestamp() * 1000),
            'death' => null,
            'abbreviation' => 'update-abbreviation',
        ]);
        $expectedCongressmanTwo = new BSONDocument([
            '_id' => 3,
            'congressman_id' => 3,
            'name' => 'name',
            'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            'death' => null,
            'abbreviation' => 'abbreviation',
        ]);
        $this->assertEquals($expectedCongressmanOne, $result[0]['congressman']);
        $this->assertEquals($expectedCongressmanTwo, $result[1]['congressman']);
    }

    public function testUpdateConstituency()
    {
        //GIVE
        $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->insertMany([
            (new PresidentSittingPresenter)->serialize([
                'president_id' => 1,
                'congressman_constituency' => [
                    'constituency_id' => 1,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
            ]),
            (new PresidentSittingPresenter)->serialize([
                'president_id' => 2,
                'congressman_constituency' => [
                    'constituency_id' => 5,
                    'name' => 'name',
                    'abbr_short' => 'abbr_short',
                    'abbr_long' => 'abbr_long',
                    'description' => 'description',
                ],
            ]),
        ]);

        // GIVEN
        (new PresidentSitting())
            ->setSourceDatabase($this->getDatabase())
            ->updateConstituency([
                'constituency_id' => 1,
                'name' => 'update-name',
                'abbr_short' => 'update-abbr_short',
                'abbr_long' => 'update-abbr_long',
                'description' => 'update-description',
            ]);

        //THEN
        $result = iterator_to_array(
            $this->getDatabase()->selectCollection(PresidentSitting::COLLECTION)->find(),
            true
        );
        $expectedConstituencyOne = new BSONDocument([
                '_id' => 1,
                'constituency_id' => 1,
                'name' => 'update-name',
                'abbr_short' => 'update-abbr_short',
                'abbr_long' => 'update-abbr_long',
                'description' => 'update-description',
        ]);
        $expectedConstituencyTwo = new BSONDocument([
                '_id' => 5,
                'constituency_id' => 5,
                'name' => 'name',
                'abbr_short' => 'abbr_short',
                'abbr_long' => 'abbr_long',
                'description' => 'description',
        ]);
        $this->assertEquals($expectedConstituencyOne, $result[0]['congressman_constituency']);
        $this->assertEquals($expectedConstituencyTwo, $result[1]['congressman_constituency']);
    }
}
