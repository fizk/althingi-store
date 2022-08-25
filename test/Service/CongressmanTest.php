<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\Congressman;
use App\DatabaseConnectionTrait;
use App\Presenter\CongressmanPresenter;
use DateTime;

class CongressmanTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreSimpleStructureCreate()
    {
        //GIVEN
        // ...

        //WHEN
        $result = (new Congressman())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'congressman_id' => 1,
                'name' => 'string',
                'birth' => '2001-01-01',
                'death' => null,
                'abbreviation' => 'abbreviation',
        ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Congressman::COLLECTION)->find([]),
            false
        );
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'congressman_id' => 1,
                'name' => 'string',
                'birth' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'death' => null,
                'abbreviation' => 'abbreviation',
            ])
        ];
        $createdResultCode = 1;
        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testGet()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Congressman::COLLECTION)->insertOne(
            (new CongressmanPresenter)->serialize([
                'congressman_id' => 1,
                'name' => 'string',
                'birth' => '2001-01-01',
                'death' => null,
                'abbreviation' => 'abbreviation',
            ]
        ));

        //WHEN
        $actual = (new Congressman())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        //THEN
        $expected = [
            '_id' => 1,
            'congressman_id' => 1,
            'name' => 'string',
            'birth' => '2001-01-01T00:00:00+00:00',
            'death' => null,
            'abbreviation' => 'abbreviation',
            ];
        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Congressman::COLLECTION)->insertMany([
            (new CongressmanPresenter)->serialize([
                'congressman_id' => 1,
                'name' => 'string',
                'birth' => '2001-01-01',
                'death' => null,
                'abbreviation' => 'abbreviation',
            ]),
            (new CongressmanPresenter)->serialize([
                'congressman_id' => 2,
                'name' => 'string',
                'birth' => '2001-01-01',
                'death' => null,
                'abbreviation' => 'abbreviation',
            ])
        ]);

        //WHEN
        $actual = (new Congressman())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $expected = [
            [
                '_id' => 1,
                'congressman_id' => 1,
                'name' => 'string',
                'birth' => '2001-01-01T00:00:00+00:00',
                'death' => null,
                'abbreviation' => 'abbreviation',

            ],
            [
                '_id' => 2,
                'congressman_id' => 2,
                'name' => 'string',
                'birth' => '2001-01-01T00:00:00+00:00',
                'death' => null,
                'abbreviation' => 'abbreviation',

            ],
        ];
        $this->assertEquals($expected, $actual);
    }
}
