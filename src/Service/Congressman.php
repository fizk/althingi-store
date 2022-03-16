<?php

namespace App\Service;

use App\Decorator\SourceDatabaseAware;
use MongoDB\Database;
use MongoDB\Model\BSONDocument;
use function App\serializeBirth;
use function App\deserializeBirth;

class Congressman implements SourceDatabaseAware
{
    const COLLECTION = 'congressman';
    private Database $database;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return $document ? [
            ...$document,
            ...deserializeBirth($document),
        ] : null;
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document)  {
            return [
                ...$document,
                ...deserializeBirth($document),
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
            '_id' => $object['congressman_id'],
            ...$object,
            ...serializeBirth($object),
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['congressman_id']],
                ['$set' => $document],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
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
