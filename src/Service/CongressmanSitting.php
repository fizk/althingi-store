<?php

namespace App\Service;

use App\Decorator\SourceDatabaseAware;
use MongoDB\Database;
use MongoDB\Model\BSONDocument;
use function App\serializeDatesRange;
use function App\deserializeDatesRange;
use function App\serializeBirth;
use function App\deserializeBirth;

class CongressmanSitting implements SourceDatabaseAware
{
    const COLLECTION = 'congressman-sitting';
    private Database $database;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return $document ? [
            ...$document,
            'assembly' => $document['assembly'] ? [
                ...$document['assembly'],
                ...deserializeDatesRange($document['assembly']),
            ] : null,
            'congressman' => $document['congressman'] ? [
                ...$document['congressman'],
                ...deserializeBirth($document['congressman']),
            ] : null,
            'constituency' => $document['constituency'] ? [
                ...$document['constituency']
            ] : null,
            'party' => $document['party'] ? [
                ...$document['party'],
            ] : null,
            ...deserializeDatesRange($document),
        ] : null;
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document)  {
            return [
                ...$document,
                'assembly' => $document['assembly'] ? [
                    ...$document['assembly'],
                    ...deserializeDatesRange($document['assembly']),
                ] : null,
                'congressman' => $document['congressman'] ? [
                    ...$document['congressman'],
                    ...deserializeBirth($document['congressman']),
                ] : null,
                'constituency' => $document['constituency'] ? [
                    ...$document['constituency']
                ] : null,
                'party' => $document['party'] ? [
                    ...$document['party'],
                ] : null,
                ...deserializeDatesRange($document),
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
            ...$object,
            '_id' => $object['session_id'],
            'assembly' => $object['assembly'] ? [
                ...$object['assembly'],
                ...serializeDatesRange($object['assembly']),
            ] : null,
            'congressman' => $object['congressman'] ? [
                ...$object['congressman'],
                ...serializeBirth($object['congressman']),
            ] : null,
            'constituency' => $object['constituency'] ? [
                ...$object['constituency']
            ] : null,
            'party' => $object['party'] ? [
                ...$object['party'],
            ] : null,
            ...serializeDatesRange($object),
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['session_id']],
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
    }

    public function updateParty(?array $party): void
    {
        if (!$party) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['party.party_id' => $party['party_id']],
                ['$set' => ['party' => $party,]],
                ['upsert' => false]
            );
    }

    public function updateCongressman(?array $congressman): void
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
                ],]],
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
                ['constituency.constituency_id' => $constituency['constituency_id']],
                ['$set' => ['constituency' => $constituency,]],
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
