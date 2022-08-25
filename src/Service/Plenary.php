<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use App\Presenter\AssemblyPresenter;
use App\Presenter\PlenaryPresenter;
use MongoDB\Model\BSONDocument;

class Plenary implements SourceDatabaseAware
{
    const COLLECTION = 'plenary';
    use SourceDatabaseTrait;

    public function get(int $assemblyId, ?int $plenaryId = null): ?array
    {
        if ($plenaryId === null) {
            /** @var \Iterator */
            $minResult = $this->getSourceDatabase()->selectCollection(self::COLLECTION)->aggregate([
                ['$match' => ['_id.assembly_id' => $assemblyId ]],
                [
                    '$group' => [
                        '_id' => [],
                        'min' => [ '$min' => '$_id.plenary_id' ]
                    ]
                ]
            ]);

            $minResult->rewind();
            $plenaryId = $minResult->current()['min'];
        }

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
        return (new PlenaryPresenter)->unserialize($document);
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
            return (new PlenaryPresenter)->unserialize($document);
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
            return (new PlenaryPresenter)->unserialize($document);
        }, iterator_to_array($documents));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = (new PlenaryPresenter)->serialize($object);

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
}
