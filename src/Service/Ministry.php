<?php

namespace App\Service;

use App\Decorator\SourceDatabaseAware;
use MongoDB\Database;
use MongoDB\Model\BSONDocument;
use function App\deserializeDatesRange;
use function App\serializeAssembly;

class Ministry implements SourceDatabaseAware
{
    const COLLECTION = 'ministry';
    private Database $database;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return $document ? [
            ...$document,
            'first' => $document['first'] ? [
                ...$document['first'],
                ...deserializeDatesRange($document['first'])
            ] : null,
            'last' => $document['last'] ? [
                ...$document['last'],
                ...deserializeDatesRange($document['last'])
            ] : null,
        ] : null;
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document) {
            return [
                ...$document,
                'first' => $document['first'] ? [
                    ...$document['first'],
                    ...deserializeDatesRange($document['first']),
                ] : null,
                'last' => $document['last'] ? [
                    ...$document['last'],
                    ...deserializeDatesRange($document['last']),
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
            '_id' => $object['ministry_id'],
            ...$object,
            'first' => $object['first']
                ? serializeAssembly($object['first'])
                : null,
            'last' => $object['last']
                ? serializeAssembly($object['last'])
                : null,
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['ministry_id']],
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
                ['first.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['first' => serializeAssembly($assembly)]],
                ['upsert' => false]
            );

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['last.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['last' => serializeAssembly($assembly)]],
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
