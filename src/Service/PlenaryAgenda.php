<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use MongoDB\Model\BSONDocument;
use function App\{deserializePlenaryAgenda, serializeAssembly, serializePlenary, serializePlenaryAgenda};

class PlenaryAgenda implements SourceDatabaseAware
{
    const COLLECTION = 'plenary-agenda';
    use SourceDatabaseTrait;

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
                    '$addFields' => [
                        'issue.assembly' => '$assembly',
                        'plenary.assembly' => '$assembly',
                    ]
                ],
            ]);
        $documents->rewind();
        $document = $documents->current();
        return $document ? deserializePlenaryAgenda($document) : null;
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
                    '$addFields' => [
                        'issue.assembly'=> '$assembly',
                        'plenary.assembly'=> '$assembly',
                    ]
                ],
        ]);

        return array_map(function (BSONDocument $document)  {
            return deserializePlenaryAgenda($document);
        }, iterator_to_array($documents));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $id = [
            'assembly_id' => (int) $object['assembly']['assembly_id'],
            'plenary_id' => (int) $object['plenary']['plenary_id'],
            'item_id' => (int) $object['item_id']
        ];
        $document = [
            '_id' => $id,
            ...serializePlenaryAgenda($object),
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $id],
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
                ['$set' => ['assembly' => serializeAssembly($assembly)]],
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
                    'plenary.plenary_id' => $plenary['plenary_id'],
                    'plenary.assembly_id' => $plenary['assembly_id'],
                ],
                ['$set' => ['plenary' => serializePlenary($plenary)]],
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

    // @todo | hook this up to the handler when it's created
    public function updateIssue(?array $issue): void
    {
        if (!$issue) {
            return;
        }

        // $this->getSourceDatabase()
        //     ->selectCollection(self::COLLECTION)
        //     ->updateMany(
        //         ['assembly.assembly_id' => $assembly['assembly_id']],
        //         ['$set' => ['assembly' => serializeAssembly($assembly)]],
        //         ['upsert' => false]
        //     );
    }
}
