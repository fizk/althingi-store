<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use App\Presenter\{SpeechPresenter, SpeechAggregatePresenter};
use MongoDB\Model\BSONDocument;

class Speech implements SourceDatabaseAware
{
    const COLLECTION = 'speech';
    use SourceDatabaseTrait;

    private int $maxWordCount = 600;
    private array $addFields = [
        'issue.assembly' => '$assembly',
        'plenary.assembly' => '$assembly',
        'duration' => [
            '$dateDiff' => [
                'startDate' => '$from',
                'endDate' => '$to',
                'unit' => 'minute',
            ]
        ]
    ];

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id.speech_id' => $id]);

        return (new SpeechPresenter)->unserialize($document);
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document)  {
            return (new SpeechPresenter)->unserialize($document);
        }, iterator_to_array(
            $this->getSourceDatabase()->selectCollection(self::COLLECTION)->find()
        ));
    }

    public function fetchByIssue(int $assemblyId, int $issueId, string $category, ?string $speechId = null): array
    {
        $documents = [];
        $nextId = null;
        $terminal = true;
        $active = false;
        $counter = 0;

        /** @var \Iterator */
        $cursor = $this->getSourceDatabase()->selectCollection(self::COLLECTION)->aggregate([
            [
                '$match' => [
                    '_id.assembly_id' => $assemblyId,
                    '_id.issue_id' => $issueId,
                    '_id.category' => $category,
                ]
            ],
            [
                '$addFields' => $this->addFields
            ],
            [
                '$sort' => ['from' => 1]
            ]
        ]);

        $cursor->rewind();

        while($cursor->valid()) {
            /** @var \MongoDB\Model\BSONDocument */
            $current = $cursor->current();

            if ($speechId === null) {
                $active = true;
            }
            if($current['speech_id'] === $speechId) {
                $active = true;
                $counter += $current['word_count'];
                $documents[] = $current;

                $cursor->next();
                if ($cursor->valid()) {
                    $next = $cursor->current();
                    $nextId = $next['speech_id'];
                    continue;
                } else {
                    $nextId = null;
                    $terminal = true;
                    break;
                }
            }
            if ($active && $counter < $this->maxWordCount) {
                $counter += $current['word_count'];
                $documents[] = $current;

                $cursor->next();
                if ($cursor->valid()) {
                    $next = $cursor->current();
                    $nextId = $next['speech_id'];
                    continue;
                } else {
                    $nextId = null;
                    $terminal = true;
                    break;
                }
            }

            $cursor->next();
            $terminal = false;
        }

        $list = array_map(function (BSONDocument $document) {
            return (new SpeechPresenter)->unserialize($document);
        }, $documents);

        return [
            'list' => $list,
            'next' => $nextId,
            'terminal' => $terminal,
        ];
    }

    public function fetchAggregateByIssue(int $assemblyId, int $issueId, string $category): array
    {
        $documents = $this->getSourceDatabase()->selectCollection(self::COLLECTION)->aggregate([
            [
                '$match' => [
                    '_id.assembly_id' => $assemblyId,
                    '_id.issue_id' => $issueId,
                    '_id.category' => $category
                ]
            ],
            [
                '$group' => [
                    '_id' => '$congressman._id',
                    'total' => ['$count' => (object)[]],
                    'congressman' => ['$first' => '$congressman'],
                    'parties' => ['$addToSet' => '$congressman_party'],
                    'constituencies' => ['$addToSet' => '$congressman_constituency'],
                    'type' => ['$addToSet' => '$congressman_type'],
                    'time' => [
                        '$sum' => [
                            '$dateDiff' => [
                                'startDate' => '$from',
                                'endDate' => '$to',
                                'unit' => 'second',
                            ]
                        ]
                    ]
                ],
            ],
            [
                '$sort' => ['time' => -1]
            ]
        ]);

        return array_map(function (BSONDocument $document) {
            return (new SpeechAggregatePresenter)->unserialize($document);
        }, iterator_to_array($documents));
    }

    public function fetchAggregateByAssembly(int $assemblyId, int $limit = 10): array
    {
        $documents = $this->getSourceDatabase()->selectCollection(self::COLLECTION)->aggregate([
            [
                '$match' => [
                    '_id.assembly_id' => $assemblyId,
                ]
            ],
            [
                '$group' => [
                    '_id' => '$congressman._id',
                    'total' => ['$count' => (object)[]],
                    'congressman' => ['$first' => '$congressman'],
                    'parties' => ['$addToSet' => '$congressman_party'],
                    'constituencies' => ['$addToSet' => '$congressman_constituency'],
                    'type' => ['$addToSet' => '$congressman_type'],
                    'time' => [
                        '$sum' => [
                            '$dateDiff' => [
                                'startDate' => '$from',
                                'endDate' => '$to',
                                'unit' => 'second',
                            ]
                        ]
                    ]
                ],
            ],
            [
                '$sort' => ['time' => -1]
            ],
            [
                '$limit' => $limit
            ]
        ]);

        return array_map(function (BSONDocument $document) {
            return (new SpeechAggregatePresenter)->unserialize($document);
        }, iterator_to_array($documents));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = (new SpeechPresenter)->serialize($object);

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $document['_id']],
                ['$set' => $document],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }
}
