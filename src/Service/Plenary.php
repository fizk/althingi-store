<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use MongoDB\Model\BSONDocument;
use function App\{deserializePlenary, serializePlenary, serializeAssembly};

class Plenary implements SourceDatabaseAware
{
    const COLLECTION = 'plenary';
    use SourceDatabaseTrait;

    public function get(int $assemblyId, int $plenaryId): ?array
    {
        // $document = $this->getSourceDatabase()
        //     ->selectCollection(self::COLLECTION)
        //     ->findOne(['_id' => [
        //         'assembly_id' => $assemblyId,
        //         'plenary_id' => $plenaryId
        //     ]]);

        // return $document ? deserializePlenary($document) : null;

        /** @var \Iterator */
        $documents = $this->getSourceDatabase()->selectCollection(self::COLLECTION)->aggregate([
            [
                '$match' => [
                    '_id.assembly_id' => $assemblyId,
                    '_id.plenary_id' => $plenaryId
                ]
            ],
            [
                '$addFields' => [
                    'duration' => [
                        '$dateDiff' => [
                            'startDate' => '$from',
                            'endDate' => '$to',
                            'unit' => 'minute',
                        ]
                    ]
                ]
            ]
        ]);
        $documents->rewind();
        $document = $documents->current();
        return $document ? deserializePlenary($document) : null;
    }

    public function fetch(): array
    {
        $documents = $this->getSourceDatabase()->selectCollection(self::COLLECTION)->aggregate([
            [
                '$addFields' => [
                    'duration' => [
                        '$dateDiff' => [
                            'startDate' => '$from',
                            'endDate' => '$to',
                            'unit' => 'minute',
                        ]
                    ]
                ]
            ]
        ]);

        return array_map(function (BSONDocument $document) {
            return deserializePlenary($document);
        }, iterator_to_array($documents));
    }

    public function fetchByAssembly(int $assemblyId): array
    {
        $documents = $this->getSourceDatabase()->selectCollection(self::COLLECTION)->aggregate([
            [
                '$match' => [
                    '_id.assembly_id' => $assemblyId,
                ]
            ],
            [
                '$addFields' => [
                    'duration' => [
                        '$dateDiff' => [
                            'startDate' => '$from',
                            'endDate' => '$to',
                            'unit' => 'minute',
                        ]
                    ]
                ]
            ]
        ]);

        return array_map(function (BSONDocument $document) {
            return deserializePlenary($document);
        }, iterator_to_array($documents));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $id = [
            'assembly_id' => $object['assembly']['assembly_id'],
            'plenary_id' => $object['plenary_id'],
        ];
        $document = [
            '_id' => $id,
            ...serializePlenary($object),
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
}
