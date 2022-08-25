<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\TestCase;
use App\Service\Party;
use App\DatabaseConnectionTrait;
use App\Presenter\PartyPresenter;

class PartyTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testStoreCreate()
    {
        //GIVEN

        //WHEN
        $result = (new Party())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'party_id' => 1,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
        ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'party_id' => 1,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
            ])
        ];
        $createdResultCode = 1;
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Party::COLLECTION)->find([]),
            false
        );

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }

    public function testStoreNoAction()
    {
        //GIVEN
        $this->getDatabase()->selectCollection(Party::COLLECTION)->insertOne(
            (new PartyPresenter)->serialize([
                'party_id' => 1,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
            ])
        );

        //WHEN
        $result = (new Party())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                'party_id' => 1,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
        ]);

        //THEN
        $expected = [
            new BSONDocument([
                '_id' => 1,
                'party_id' => 1,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
            ])
        ];
        $noActionResultCode = 0;
        $actual = iterator_to_array(
            $this->getDatabase()->selectCollection(Party::COLLECTION)->find([]),
            false
        );

        $this->assertEquals($expected, $actual);
        $this->assertEquals($noActionResultCode, $result);
    }

    public function testGetNotFound()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Party::COLLECTION)->insertOne(
            (new PartyPresenter)->serialize([
                'party_id' => 1,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
            ])
        );

        //WHEN
        $expected = null;
        $actual = (new Party())
            ->setSourceDatabase($this->getDatabase())
            ->get(2);

        //THEN
        $this->assertEquals($expected, $actual);
    }

    public function testGet()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Party::COLLECTION)->insertOne(
            (new PartyPresenter)->serialize([
                'party_id' => 1,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
            ])
        );

        //WHEN
        $expected = [
            '_id' => 1,
            'party_id' => 1,
            'name' => 'name-1',
            'abbr_short' => null,
            'abbr_long' => null,
            'color' => null,
        ];
        $actual = (new Party())
            ->setSourceDatabase($this->getDatabase())
            ->get(1);

        //THEN
        $this->assertEquals($expected, $actual);
    }

    public function testFetch()
    {
        //GIVE
        $this->getDatabase()->selectCollection(Party::COLLECTION)->insertMany([
            (new PartyPresenter)->serialize([
                'party_id' => 1,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
            ]),
            (new PartyPresenter)->serialize([
                'party_id' => 2,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
            ]),
        ]);

        //WHEN
        $expected = [
            [
                '_id' => 1,
                'party_id' => 1,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
            ],
            [
                '_id' => 2,
                'party_id' => 2,
                'name' => 'name-1',
                'abbr_short' => null,
                'abbr_long' => null,
                'color' => null,
            ]
        ];
        $actual = (new Party())
            ->setSourceDatabase($this->getDatabase())
            ->fetch();

        //THEN
        $this->assertEquals($expected, $actual);
    }
}
