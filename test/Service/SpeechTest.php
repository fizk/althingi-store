<?php

namespace App\Service;

use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;
use App\Service\Speech;
use App\DatabaseConnectionTrait;
use App\Presenter\SpeechPresenter;
use DateTime;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

class SpeechTest extends TestCase
{
    use DatabaseConnectionTrait;

    public function testFetchByIssueReturnsEmptySet()
    {
        // GIVEN

        // WHEN
        $actual = (new Speech())
            ->setSourceDatabase($this->getDatabase())
            ->fetchByIssue(1,1,'a');

        // THEN
        $expected = [
            'list' => [],
            'next' => null,
            'terminal' => true
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testFetchByIssueReturnsCompleteSet()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Speech::COLLECTION)->insertMany([
            (new SpeechPresenter)->serialize([
                "speech_id"  => "00001",
                "assembly"  => [
                    "assembly_id"  => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                "issue"  => [
                    "issue_id"  => 1,
                    "category"  => 'a',
                    "assembly_id"  => 1,
                ],
                "iteration"  => "1",
                "plenary"  => [
                    "assembly_id"  => 1,
                    "plenary_id"  => 3,
                ],
                "speech_id"  => "00001",
                "word_count"  => 500
            ]),
            (new SpeechPresenter)->serialize([
                "speech_id"  => "00002",
                "assembly"  => [
                    "assembly_id"  => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ],
                'from' => new UTCDateTime((new DateTime('2001-01-02'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-02'))->getTimestamp() * 1000),
                "issue"  => [
                    "assembly_id"  => 1,
                    "issue_id"  => 1,
                    "category"  => 'a',
                ],
                "iteration"  => "1",
                "plenary"  => [
                    "assembly_id"  => 1,
                    "plenary_id"  => 3,
                ],
                "speech_id"  => "00002",
                "word_count"  => 500
            ]),
        ]);

        // WHEN
        $actual = (new Speech())
            ->setSourceDatabase($this->getDatabase())
            ->fetchByIssue(1,1,'a');

        $this->assertEquals(null, $actual['next']);
        $this->assertEquals(true, $actual['terminal']);
        $this->assertCount(2, $actual['list']);
    }

    public function testFetchByIssueReturnsNotCompleteSet()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Speech::COLLECTION)->insertMany([
            (new SpeechPresenter)->serialize([
                "speech_id"  => "00001",
                "assembly"  => [
                    "assembly_id"  => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                "issue"  => [
                    "assembly_id"  => 1,
                    "issue_id"  => 1,
                    "category"  => "a",
                ],
                "iteration"  => "1",
                "plenary"  => [
                    "assembly_id"  => 1,
                    "plenary_id"  => 3,
                ],
                "speech_id"  => "00001",
                "word_count"  => 700
            ]),
            (new SpeechPresenter)->serialize([
                "speech_id"  => "00002",
                "assembly"  => [
                    "assembly_id"  => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-02',
                'to' => '2001-01-02',
                "issue"  => [
                    "assembly_id"  => 1,
                    "issue_id"  => 1,
                    "category"  => "a",
                ],
                "iteration"  => "1",
                "plenary"  => [
                    "assembly_id"  => 1,
                    "plenary_id"  => 3,
                ],
                "speech_id"  => "00002",
                "word_count"  => 500
            ]),
        ]);

        $i = iterator_to_array($this->getDatabase()->selectCollection(Speech::COLLECTION)->find());
        // WHEN
        $actual = (new Speech())
            ->setSourceDatabase($this->getDatabase())
            ->fetchByIssue(1,1,'a');

        $this->assertEquals('00002', $actual['next']);
        $this->assertEquals(false, $actual['terminal']);
        $this->assertCount(1, $actual['list']);
    }

    public function testFetchByIssueReturnsMiddleEntry()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Speech::COLLECTION)->insertMany([
            (new SpeechPresenter)->serialize([
                "assembly"  => [
                    "assembly_id"  => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                "issue"  => [
                    "assembly_id"  => 1,
                    "issue_id"  => 1,
                    "category"  => "a",
                ],
                "iteration"  => "1",
                "plenary"  => [
                    "assembly_id"  => 1,
                    "plenary_id"  => 3,
                ],
                "speech_id"  => "00001",
                "word_count"  => 500
            ]),
            (new SpeechPresenter)->serialize([
                "assembly"  => [
                    "assembly_id"  => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-02',
                'to' => '2001-01-02',
                "issue"  => [
                    "assembly_id"  => 1,
                    "issue_id"  => 1,
                    "category"  => "a",
                ],
                "iteration"  => "1",
                "plenary"  => [
                    "assembly_id"  => 1,
                    "plenary_id"  => 3,
                ],
                "speech_id"  => "00002",
                "word_count"  => 700
            ]),
            (new SpeechPresenter)->serialize([
                "assembly"  => [
                    "assembly_id"  => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-03',
                'to' => '2001-01-03',
                "issue"  => [
                    "assembly_id"  => 1,
                    "issue_id"  => 1,
                    "category"  => "a",
                ],
                "iteration"  => "1",
                "plenary"  => [
                    "assembly_id"  => 1,
                    "plenary_id"  => 3,
                ],
                "speech_id"  => "00003",
                "word_count"  => 500
            ]),
        ]);

        // WHEN
        $actual = (new Speech())
            ->setSourceDatabase($this->getDatabase())
            ->fetchByIssue(1, 1, 'a', '00002');

        // THEN
        $this->assertEquals('00003', $actual['next']);
        $this->assertEquals(false, $actual['terminal']);
        $this->assertCount(1, $actual['list']);
    }

    public function testFetchByIssueReturnsLastTwoEntries()
    {
        // GIVEN
        $this->getDatabase()->selectCollection(Speech::COLLECTION)->insertMany([
            (new SpeechPresenter)->serialize([
                "assembly"  => [
                    "assembly_id"  => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                "issue"  => [
                    "assembly_id"  => 1,
                    "issue_id"  => 1,
                    "category"  => "a",
                ],
                "iteration"  => "1",
                "plenary"  => [
                    "assembly_id"  => 1,
                    "plenary_id"  => 3,
                ],
                "speech_id"  => "00001",
                "word_count"  => 500
            ]),
            (new SpeechPresenter)->serialize([
                "assembly"  => [
                    "assembly_id"  => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-02',
                'to' => '2001-01-02',
                "issue"  => [
                    "assembly_id"  => 1,
                    "issue_id"  => 1,
                    "category"  => "a",
                ],
                "iteration"  => "1",
                "plenary"  => [
                    "assembly_id"  => 1,
                    "plenary_id"  => 3,
                ],
                "speech_id"  => "00002",
                "word_count"  => 100
            ]),
            (new SpeechPresenter)->serialize([
                "assembly"  => [
                    "assembly_id"  => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-03',
                'to' => '2001-01-03',
                "issue"  => [
                    "assembly_id"  => 1,
                    "issue_id"  => 1,
                    "category"  => "a",
                ],
                "iteration"  => "1",
                "plenary"  => [
                    "assembly_id"  => 1,
                    "plenary_id"  => 3,
                ],
                "speech_id"  => "00003",
                "word_count"  => 100
            ]),
        ]);

        // WHEN
        $actual = (new Speech())
            ->setSourceDatabase($this->getDatabase())
            ->fetchByIssue(1, 1, 'a', '00002');

        // THEN
        $this->assertEquals(null, $actual['next']);
        $this->assertEquals(true, $actual['terminal']);
        $this->assertCount(2, $actual['list']);
    }

    public function testStore()
    {
        // GIVE

        //WHEN
        $result = (new Speech())
            ->setSourceDatabase($this->getDatabase())
            ->store([
                "assembly"  => [
                    "assembly_id"  => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                'from' => '2001-01-01',
                'to' => '2001-01-01',
                "issue"  => [
                    "assembly_id"  => 1,
                    "issue_id"  => 1,
                    "category"  => "a",
                ],
                "iteration"  => "1",
                "plenary"  => [
                    "assembly_id"  => 1,
                    "plenary_id"  => 3,
                ],
                "speech_id"  => "00001",
                "word_count"  => 500
            ]);

        // THEN
        $actual = iterator_to_array($this->getDatabase()->selectCollection(Speech::COLLECTION)->find(), true);
        $expected = [
            new BSONDocument([
                '_id' => new BSONDocument([
                    "assembly_id"  => 1,
                    "issue_id"  => 1,
                    "category"  => "a",
                    "speech_id"  => "00001",
                ]),
                "assembly"  => new BSONDocument([
                    "_id"  => 1,
                    "assembly_id"  => 1,
                    'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                    'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                ]),
                "congressman" => null,
                "congressman_constituency" => null,
                "congressman_party" => null,
                "congressman_type" => null,
                "iteration" => null,
                'from' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'to' => new UTCDateTime((new DateTime('2001-01-01'))->getTimestamp() * 1000),
                'text' => null,
                'type' => null,
                'validated' => false,
                "issue"  => new BSONDocument([
                    "_id"  => new BSONDocument([
                        "assembly_id"  => 1,
                        "issue_id"  => 1,
                        "category"  => "a",
                    ]),
                    "issue_id"  => 1,
                    "category"  => "a",
                    'name' => null,
                    'sub_name' => null,
                    'type' => null,
                    'type_name' => null,
                    'type_subname' => null,
                    'status' => null,
                    'question' => null,
                    'goal' => null,
                    'major_changes' => null,
                    'changes_in_law' => null,
                    'costs_and_revenues' => null,
                    'deliveries' => null,
                    'additional_information' => null,
                    'assembly' => null,
                    'congressman' => null,
                    'proponents' => new BSONArray([]),
                    'content_categories' => new BSONArray([]),
                    'content_super_categories' => new BSONArray([]),
                ]),
                "iteration"  => "1",
                "plenary"  => new BSONDocument([
                    '_id' => new BSONDocument([
                        "assembly_id"  => 1,
                        "plenary_id"  => 3,
                    ]),
                    "plenary_id"  => 3,
                    'from' => null,
                    'to' => null,
                    'name' => null,
                ]),
                "speech_id"  => "00001",
                "word_count"  => 500
            ])
        ];
        $createdResultCode = 1;

        $this->assertEquals($expected, $actual);
        $this->assertEquals($createdResultCode, $result);
    }
}
