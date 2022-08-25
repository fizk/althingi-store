<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\Plenary;
use App\DatabaseConnectionTrait;
use App\Presenter\PlenaryPresenter;
use DateTime;

class PlenaryTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreSimpleStructureCreate()
    {
        //GIVEN

        //WHEN
        $result = (new Plenary())
            ->setSourceDatabase($this->getDatabase())
            ->store(
                [
                    'plenary_id' => 1,
                    'assembly' => [
                        'assembly_id' =>  2,
                        'from' => '2001-01-01T00:00:00+00:00',
                        'to' => '2001-01-01T00:00:00+00:00',
                    ],
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                    'name' => 'some name',
                ]
        );

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => new BSONDocument([
                    'assembly_id' => 2,
                    'plenary_id' => 1
                ]),
                'plenary_id' => 1,
                'assembly' => new BSONDocument([
                    '_id' =>  2,
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'name' => 'some name',
            ])
        ];
        $createdResultCode = 1;
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Plenary::COLLECTION)->find([]),
            false
        );

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testGet()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Plenary::COLLECTION)->insertOne(
            (new PlenaryPresenter)->serialize([
                'plenary_id' => 1,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                'name' => 'some name',
            ])
        );

        //WHEN
        $actual = (new Plenary())
            ->setSourceDatabase($this->getDatabase())
            ->get(2, 1);

        //THEN
        $expected = [
            '_id' => [
                'assembly_id' => 2,
                'plenary_id' => 1,
            ],
            'plenary_id' => 1,
            'assembly' => [
                '_id' =>  2,
                'assembly_id' =>  2,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
            'from' => '2001-01-01T00:00:00+00:00',
            'to' => '2001-01-01T00:00:00+00:00',
            'duration' => 0,
            'name' => 'some name',
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Plenary::COLLECTION)->insertMany([
            (new PlenaryPresenter)->serialize([
                'plenary_id' => 1,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                'name' => 'some name',
            ]),
            (new PlenaryPresenter)->serialize([
                'plenary_id' => 2,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                'name' => 'some name',
            ]),
        ]);

        //WHEN
        $actual = (new Plenary())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $expected = [
            [
                '_id' => [
                    'assembly_id' => 2,
                    'plenary_id' => 1,
                ],
                'plenary_id' => 1,
                'assembly' => [
                    '_id' =>  2,
                    'assembly_id' =>  2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
                'duration' => 0,
                'name' => 'some name',
            ],
            [
                '_id' => [
                    'assembly_id' => 2,
                    'plenary_id' => 2,
                ],
                'plenary_id' => 2,
                'assembly' => [
                    '_id' =>  2,
                    'assembly_id' =>  2,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
                'duration' => 0,
                'name' => 'some name',
            ],
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testFetchByAssembly()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Plenary::COLLECTION)->insertMany([
            (new PlenaryPresenter)->serialize([
                'plenary_id' => 1,
                'assembly' => [
                    'assembly_id' =>  1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                'name' => 'some name',
            ]),
            (new PlenaryPresenter)->serialize([
                'plenary_id' => 2,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                'name' => 'some name',
            ]),
        ]);

        //WHEN
        $actual = (new Plenary())
            ->setSourceDatabase($this->getDatabase())
            ->fetchByAssembly(1);

        //THEN
        $expected = [
            [
                '_id' => [
                    'assembly_id' => 1,
                    'plenary_id' => 1,
                ],
                'plenary_id' => 1,
                'assembly' => [
                    '_id' =>  1,
                    'assembly_id' =>  1,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
                'duration' => 0,
                'name' => 'some name',
            ],
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateAssembly()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Plenary::COLLECTION)->insertMany([
            (new PlenaryPresenter)->serialize([
                'plenary_id' => 1,
                'assembly' => [
                    'assembly_id' =>  1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                'name' => 'some name',
            ]),
            (new PlenaryPresenter)->serialize([
                'plenary_id' => 2,
                'assembly' => [
                    'assembly_id' =>  2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                'name' => 'some other name',
            ]),
        ]);

        //WHEN
        (new Plenary())
            ->setSourceDatabase($this->getDatabase())
            ->updateAssembly([
                'assembly_id' => 2,
                'from' => '1978-01-01',
                'to' => '1978-01-01',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Plenary::COLLECTION)->find(),
            false
        );

        $expected = [
            new BSONDocument([
                '_id' => new BSONDocument([
                    'assembly_id' =>  1,
                    'plenary_id' => 1
                ]),
                'plenary_id' => 1,
                'assembly' => new BSONDocument([
                    '_id' =>  1,
                    'assembly_id' =>  1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'name' => 'some name',
            ]),
            new BSONDocument([
                '_id' => new BSONDocument([
                    'assembly_id' =>  2,
                    'plenary_id' => 2
                ]),
                'plenary_id' => 2,
                'assembly' => new BSONDocument([
                    '_id' =>  2,
                    'assembly_id' =>  2,
                    'from' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                ]),
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'name' => 'some other name',
            ]),
        ];

        $this->assertEquals($expected, $actual);
    }
}
