<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use App\Presenter\{
    EmbeddedIssuePresenter,
    AssemblyPresenter,
    EmbeddedPlenaryPresenter,
    PlenaryAgendaPresenter
};
use MongoDB\Model\BSONDocument;

class PlenaryAgenda implements SourceDatabaseAware
{
    const COLLECTION = 'plenary-agenda';
    use SourceDatabaseTrait;

    private $addFields = [
        'issue.assembly' => '$assembly',
        'plenary.assembly' => '$assembly',
        'issue._id' => [
            'assembly_id' => '$assembly.assembly_id',
            'issue_id' => '$issue.issue_id',
            'category' => '$issue.category',
        ],
        'plenary._id' => [
            'assembly_id' => '$assembly.assembly_id',
            'plenary_id' => '$plenary.plenary_id',
        ],
        'plenary.duration' => [
            '$dateDiff' => [
                'startDate' => '$plenary.from',
                'endDate' => '$plenary.to',
                'unit' => 'minute',
            ]
        ]
    ];

    public function get(int $assemblyId, int $plenaryId, int $itemId): ?array
    {
        /** @var \Iterator */
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => [
                        '_id.assembly_id' => $assemblyId,
                        '_id.plenary_id' => $plenaryId,
                        '_id.item_id' => $itemId,
                    ]
                ],
                [
                    '$addFields' => $this->addFields
                ],
            ]);
        $documents->rewind();
        $document = $documents->current();
        return (new PlenaryAgendaPresenter)->unserialize($document);
    }

    public function fetchByPlenary(int $assemblyId, int $plenaryId): array
    {
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => [
                        '_id.assembly_id' => $assemblyId,
                        '_id.plenary_id' => $plenaryId
                    ]
                ],
                [
                    '$addFields' => $this->addFields
                ],
        ]);

        return array_map(function (BSONDocument $document)  {
            return (new PlenaryAgendaPresenter)->unserialize($document);
        }, iterator_to_array($documents));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = (new PlenaryAgendaPresenter)->serialize($object);

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $document['_id']],
                ['$set' => $document],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }

    public function updateAssembly(?array $assembly): void
    {
        if (!$assembly) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['assembly' => (new AssemblyPresenter)->serialize($assembly)]],
                ['upsert' => false]
            );
    }

    public function updatePlenary(?array $plenary): void
    {
        if (!$plenary) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                [
                    'plenary._id.assembly_id' => $plenary['assembly_id'],
                    'plenary._id.plenary_id' => $plenary['plenary_id'],
                ],
                ['$set' => ['plenary' => (new EmbeddedPlenaryPresenter)->serialize($plenary)]],
                ['upsert' => false]
            );
    }

    /**
     * @todo
     * There might be a bug in the aggregator where the congressmen
     * are not registered
     */
    public function updateCongressman(?array $congressman): void
    {
        if (!$congressman) {
            return;
        }

        // "answerer" : null,
        // "counter_answerer" : null,
        // "instigator" : null,
        // "posed" : null,

        // $this->getSourceDatabase()
        //     ->selectCollection(self::COLLECTION)
        //     ->updateMany(
        //         ['assembly.assembly_id' => $assembly['assembly_id']],
        //         ['$set' => ['assembly' => serializeAssembly($assembly)]],
        //         ['upsert' => false]
        //     );
    }

    /**
     * @todo
     * There might be a bug in the aggregator where the congressmen
     * are not registered
     */
    public function updateParty(?array $party): void
    {
        if (!$party) {
            return;
        }
        // "answerer_party" : null,
        // "counter_answerer_party" : null,
        // "instigator_party" : null,
        // "posed_party" : null,

        // $this->getSourceDatabase()
        //     ->selectCollection(self::COLLECTION)
        //     ->updateMany(
        //         ['assembly.assembly_id' => $assembly['assembly_id']],
        //         ['$set' => ['assembly' => serializeAssembly($assembly)]],
        //         ['upsert' => false]
        //     );
    }

    /**
     * @todo
     * There might be a bug in the aggregator where the congressmen
     * are not registered
     */
    public function updateConstituency(?array $constituency): void
    {
        if (!$constituency) {
            return;
        }

        // "answerer_constituency" : null,
        // "counter_answerer_constituency" : null,
        // "instigator_constituency" : null,
        // "posed_constituency" : null,

        // $this->getSourceDatabase()
        //     ->selectCollection(self::COLLECTION)
        //     ->updateMany(
        //         ['assembly.assembly_id' => $assembly['assembly_id']],
        //         ['$set' => ['assembly' => serializeAssembly($assembly)]],
        //         ['upsert' => false]
        //     );
    }

    public function updateIssue(?array $issue): void
    {
        if (!$issue) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                [
                    'issue._id.assembly_id' => $issue['assembly_id'],
                    'issue._id.issue_id' => $issue['issue_id'],
                    'issue._id.category' => $issue['category'],
                ],
                ['$set' => ['issue' => (new EmbeddedIssuePresenter)->serialize($issue)]],
                ['upsert' => false]
            );
    }
}
