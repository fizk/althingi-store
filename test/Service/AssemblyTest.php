<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\Assembly;
use App\DatabaseConnectionTrait;
use App\Presenter\AssemblyPresenter;
use DateTime;

class AssemblyTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreSimpleStructureCreate()
    {
        //GIVEN

        //WHEN
        $result = (new Assembly())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'assembly_id' => 1,
                'from' => null,
                'to' => null,
            ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'assembly_id' => 1,
                'from' => null,
                'to' => null,
            ])
        ];
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Assembly::COLLECTION)->find([]),
            false
        );
        $createdResultCode = 1;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreWithDateCreate()
    {
        //GIVEN

        //WHEN
        $result = (new Assembly())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'assembly_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
            ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'assembly_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ])
        ];
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Assembly::COLLECTION)->find([]),
            false
        );
        $createdResultCode = 1;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreNoAction()
    {
        //GIVEN
        $this->getDatabase()->selectCollection(Assembly::COLLECTION)->insertOne(
            (new AssemblyPresenter())->serialize([
                'assembly_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
            ]
        ));

        //WHEN
        $result = (new Assembly())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'assembly_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
            ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'assembly_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
            ])
        ];
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Assembly::COLLECTION)->find([]),
            false
        );
        $noActionResultCode = 0;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($noActionResultCode, $result);
    }

    public function testStoreWithDateUpdate()
    {
        //GIVEN
        $this->getDatabase()->selectCollection(Assembly::COLLECTION)->insertOne(
            (new AssemblyPresenter)->serialize([
                'assembly_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
            ])
        );

        //WHEN
        $result = (new Assembly())
        ->setSourceDatabase($this->getDatabase())
        ->store([
            'assembly_id' => 1,
            'from' => '2002-01-01',
            'to' => '2002-01-01',
        ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'assembly_id' => 1,
                'from' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
            ])
        ];
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Assembly::COLLECTION)->find([]),
            false
        );
        $updateResultCode = 2;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($updateResultCode, $result);
    }

    public function testGetNotFound()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Assembly::COLLECTION)->insertOne(
            (new AssemblyPresenter)->serialize([
                'assembly_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
            ])
        );

        //WHEN
        $actual = (new Assembly())
            ->setSourceDatabase($this->getDatabase())
            ->get(2);

        //THEN
        $expected = null;
        $this->assertEquals($expected, $actual);
    }

    public function testGet()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Assembly::COLLECTION)->insertOne(
            (new AssemblyPresenter)->serialize([
                'assembly_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
            ])
        );

        //WHEN
        $actual = (new Assembly())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        //THEN
        $expected = [
            '_id' => 1,
            'assembly_id' => 1,
            'from' => '2001-01-01T00:00:00+00:00',
            'to' => '2001-01-01T00:00:00+00:00',
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Assembly::COLLECTION)->insertMany([
            (new AssemblyPresenter)->serialize([
                'assembly_id' => 1,
                'from' => '2001-01-01',
                'to' => '2001-01-01',
            ]),
            (new AssemblyPresenter)->serialize([
                'assembly_id' => 2,
                'from' => '2002-01-01',
                'to' => '2002-01-01',
            ]),
        ]);

        //WHEN
        $actual = (new Assembly())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $expected = [
            [
                '_id' => 2,
                'assembly_id' => 2,
                'from' => '2002-01-01T00:00:00+00:00',
                'to' => '2002-01-01T00:00:00+00:00',
            ],
            [
                '_id' => 1,
                'assembly_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
        ];

        $this->assertEquals($expected, $actual);
    }
}
