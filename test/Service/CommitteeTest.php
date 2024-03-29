<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\Committee;
use App\DatabaseConnectionTrait;
use App\Presenter\CommitteePresenter;
use DateTime;

class CommitteeTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreCreated()
    {
        //GIVE
        // ...

        //WHEN
        $result = (new Committee())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                '_id' => 1,
                'committee_id' => 1,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2002-01-01',
                ],
                'last' => [
                    'assembly_id' => 2,
                    'from' => '2003-01-01',
                    'to' => '2004-01-01',
                ],
        ]);

        //THEN
        $actual = array_map(function(BSONDocument $item) {
            return $item->getArrayCopy();
        }, iterator_to_array($this->getDatabase()->selectCollection(Committee::COLLECTION)->find([]), false));

        $expected = [[
            '_id' => 1,
            'committee_id' => 1,
            'name' => 'name',
            'abbr_long' => 'abbr_long',
            'abbr_short' => 'abbr_short',
            'first' => new BSONDocument([
                '_id' => 1,
                'assembly_id' => 1,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2002-01-01'))->getTimestamp() * 1000),
            ]),
            'last' => new BSONDocument([
                '_id' => 2,
                'assembly_id' => 2,
                'from' => new UTCDateTime((new DateTime('2003-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2004-01-01'))->getTimestamp() * 1000),
            ]),
        ]];

        $createdResultCode = 1;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testGet()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Committee::COLLECTION)->insertOne(
            (new CommitteePresenter)->serialize([
                'committee_id' => 1,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'last' => null,
            ])
        );

        //WHEN
        $actual = (new Committee())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        //THEN
        $expected = [
            '_id' => 1,
            'committee_id' => 1,
            'name' => 'name',
            'abbr_long' => 'abbr_long',
            'abbr_short' => 'abbr_short',
            'first' => [
                '_id' => 1,
                'assembly_id' => 1,
                'from' => '2001-01-01T00:00:00+00:00',
                'to' => '2001-01-01T00:00:00+00:00',
            ],
            'last' => null,
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testGetNotFound()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Committee::COLLECTION)->insertOne(
            (new CommitteePresenter)->serialize([
                'committee_id' => 1,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'last' => null,
            ])
        );

        //WHEN
        $actual = (new Committee())
            ->setSourceDatabase($this->getDatabase())
            ->get(2);

        //THEN
        $expected = null;

        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Committee::COLLECTION)->insertMany(
            [
            (new CommitteePresenter)->serialize([
                'committee_id' => 1,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'last' => null,
            ]),
            (new CommitteePresenter)->serialize([
                'committee_id' => 2,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'last' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'first' => null,
            ])
        ]);

        //WHEN
        $actual = (new Committee())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $expected = [
            [
                '_id' => 1,
                'committee_id' => 1,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'first' => [
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'last' => null,
            ], [
                '_id' => 2,
                'committee_id' => 2,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'last' => [
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2001-01-01T00:00:00+00:00',
                    'to' => '2001-01-01T00:00:00+00:00',
                ],
                'first' => null,
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateAssembly()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Committee::COLLECTION)->insertMany([
            (new CommitteePresenter)->serialize([
                'committee_id' => 1,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'first' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'last' => [
                    'assembly_id' => 2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
            ]),
            (new CommitteePresenter)->serialize([
                'committee_id' => 2,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'first' => [
                    'assembly_id' => 2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'last' => null,
            ]),
            (new CommitteePresenter)->serialize([
                '_id' => 3,
                'committee_id' => 3,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'last' => [
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'first' => null,
            ]),
        ]);

        //WHEN
        (new Committee())
            ->setSourceDatabase($this->getDatabase())
            ->updateAssembly([
                'assembly_id' => 2,
                'from' => '1978-01-01',
                'to' => '1978-01-01',
            ]);

        //THEN
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Committee::COLLECTION)->find([]),
            false
        );

        $expected = [
            new BSONDocument([
                '_id' => 1,
                'committee_id' => 1,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'first' => new BSONDocument([
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'last' => new BSONDocument([
                    '_id' => 2,
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                ]),
            ]),
            new BSONDocument([
                '_id' => 2,
                'committee_id' => 2,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'first' => new BSONDocument([
                    '_id' => 2,
                    'assembly_id' => 2,
                    'from' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('1978-01-01'))->getTimestamp() * 1000),
                ]),
                'last' => null,
            ]),
            new BSONDocument([
                '_id' => 3,
                'committee_id' => 3,
                'name' => 'name',
                'abbr_long' => 'abbr_long',
                'abbr_short' => 'abbr_short',
                'last' => new BSONDocument([
                    '_id' => 1,
                    'assembly_id' => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                'first' => null,
            ]),
        ];

        $this->assertEquals($expected, $actual);
    }
}
