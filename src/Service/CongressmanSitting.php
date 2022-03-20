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
            'congressman_constituency' => $document['congressman_constituency'] ? [
                ...$document['congressman_constituency']
            ] : null,
            'congressman_party' => $document['congressman_party'] ? [
                ...$document['congressman_party'],
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
                'congressman_constituency' => $document['congressman_constituency'] ? [
                    ...$document['congressman_constituency']
                ] : null,
                'congressman_party' => $document['congressman_party'] ? [
                    ...$document['congressman_party'],
                ] : null,
                ...deserializeDatesRange($document),
            ];
        }, iterator_to_array(
            $this->getSourceDatabase()->selectCollection(self::COLLECTION)->find()
        ));
    }

    public function fetchPartiesByAssembly(int $assemblyId)
    {
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => [
                        'assembly.assembly_id' => $assemblyId
                    ]
                ],
                [
                    '$group' => [
                        '_id' => '$congressman_party.party_id',
                        'party' => ['$first' => '$congressman_party']
                    ]
                ],
                [
                    '$replaceRoot' => ['newRoot' => '$party']
                ]
        ]);

        return array_map(function (BSONDocument $item) {
            return $item->getArrayCopy();
        },iterator_to_array($documents));
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
            'congressman_constituency' => $object['congressman_constituency'] ? [
                ...$object['congressman_constituency']
            ] : null,
            'congressman_party' => $object['congressman_party'] ? [
                ...$object['congressman_party'],
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
                ['congressman_party.party_id' => $party['party_id']],
                ['$set' => ['congressman_party' => $party,]],
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
                ['congressman_constituency.constituency_id' => $constituency['constituency_id']],
                ['$set' => ['congressman_constituency' => $constituency,]],
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
