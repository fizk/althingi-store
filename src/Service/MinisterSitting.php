<?php

namespace App\Service;

use App\Decorator\SourceDatabaseAware;
use MongoDB\Database;
use MongoDB\Model\BSONDocument;
use function App\serializeDatesRange;
use function App\deserializeDatesRange;
use function App\serializeBirth;
use function App\deserializeBirth;
use function App\serializeAssembly;

class MinisterSitting implements SourceDatabaseAware
{
    const COLLECTION = 'minister-sitting';
    private Database $database;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return $document ? [
            ...$document,
            ...deserializeDatesRange($document),
            'assembly' => $document['assembly'] ? [
                ...$document['assembly'],
                ...deserializeDatesRange($document['assembly']),
            ] : null,
            'ministry' => $document['ministry'] ? [
                ...$document['ministry'],
            ] : null,
            'congressman' => $document['congressman'] ? [
                ...$document['congressman'],
                ...deserializeBirth($document['congressman']),
            ] : null,
            'congressman_party' => $document['congressman_party'] ? [
                ...$document['congressman_party']
            ] : null,
            'congressman_constituency' => $document['congressman_constituency'] ? [
                ...$document['congressman_constituency']
            ] : null,
            'first_ministry_assembly' => $document['first_ministry_assembly'] ? [
                ...$document['first_ministry_assembly'],
                ...deserializeDatesRange($document['first_ministry_assembly']),
            ] : null,
            'last_ministry_assembly' => $document['last_ministry_assembly'] ? [
                ...$document['last_ministry_assembly'],
                ...deserializeDatesRange($document['last_ministry_assembly']),
            ] : null,
        ] : null;
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document) {
            return [
                ...$document,
                ...deserializeDatesRange($document),
                'assembly' => $document['assembly'] ? [
                    ...$document['assembly'],
                    ...deserializeDatesRange($document['assembly']),
                ] : null,
                'ministry' => $document['ministry'] ? [
                    ...$document['ministry'],
                ] : null,
                'congressman' => $document['congressman'] ? [
                    ...$document['congressman'],
                    ...deserializeBirth($document['congressman']),
                ] : null,
                'congressman_party' => $document['congressman_party'] ? [
                    ...$document['congressman_party']
                ] : null,
                'congressman_constituency' => $document['congressman_constituency'] ? [
                    ...$document['congressman_constituency']
                ] : null,
                'first_ministry_assembly' => $document['first_ministry_assembly'] ? [
                    ...$document['first_ministry_assembly'],
                    ...deserializeDatesRange($document['first_ministry_assembly']),
                ] : null,
                'last_ministry_assembly' => $document['last_ministry_assembly'] ? [
                    ...$document['last_ministry_assembly'],
                    ...deserializeDatesRange($document['last_ministry_assembly']),
                ] : null,
            ];
        }, iterator_to_array(
            $this->getSourceDatabase()->selectCollection(self::COLLECTION)->find()
        ));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = [
            '_id' => $object['minister_sitting_id'],
            ...$object,
            ...serializeDatesRange($object),
            'assembly' => $object['assembly']
                ? serializeAssembly($object['assembly'])
                : null,
            'ministry' => $object['ministry'] ? [
                ...$object['ministry'],
            ] : null,
            'congressman' => $object['congressman'] ? [
                ...$object['congressman'],
                ...serializeBirth($object['congressman']),
            ] : null,
            'congressman_constituency' => $object['congressman_constituency'] ? [
                ...$object['congressman_constituency']
            ] : null,
            'congressman_party' => $object['congressman_party'] ? [
                ...$object['congressman_party'],
            ] : null,
            'first_ministry_assembly' => $object['first_ministry_assembly']
                ? serializeAssembly($object['first_ministry_assembly'])
                : null,
            'last_ministry_assembly' => $object['last_ministry_assembly']
                ? serializeAssembly($object['last_ministry_assembly'])
                : null,
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['minister_sitting_id']],
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
                ['$set' => ['assembly' => [
                    ...$assembly,
                    ...serializeDatesRange($assembly),
                ]]],
                ['upsert' => false]
            );

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['first_ministry_assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['first_ministry_assembly' => [
                    ...$assembly,
                    ...serializeDatesRange($assembly),
                ]]],
                ['upsert' => false]
            );

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['last_ministry_assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['last_ministry_assembly' => [
                    ...$assembly,
                    ...serializeDatesRange($assembly),
                ]]],
                ['upsert' => false]
            );
    }

    public function updateParty(?array $party): void
    {
        if (!$party) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['congressman_party.party_id' => $party['party_id']],
                ['$set' => ['congressman_party' => [
                    ...$party,
                ]]],
                ['upsert' => false]
            );
    }

    public function updateConstituency(?array $constituency): void
    {
        if (!$constituency) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['congressman_constituency.constituency_id' => $constituency['constituency_id']],
                ['$set' => ['congressman_constituency' => [
                    ...$constituency,
                ]]],
                ['upsert' => false]
            );
    }

    public function updateMinistry(?array $ministry)
    {
        if (!$ministry) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['ministry.ministry_id' => $ministry['ministry_id']],
                ['$set' => ['ministry' => [
                    ...$ministry,
                ]]],
                ['upsert' => false]
            );
    }

    public function updateCongressman($congressman): void
    {
        if (!$congressman) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['congressman.congressman_id' => $congressman['congressman_id']],
                ['$set' => ['congressman' => [
                    ...$congressman,
                    ...serializeBirth($congressman)
                ]]],
                ['upsert' => false]
            );
    }

    public function getSourceDatabase(): Database
    {
        return $this->database;
    }

    public function setSourceDatabase(Database $database): self
    {
        $this->database = $database;
        return $this;
    }
}
