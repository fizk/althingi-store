<?php

namespace App\Service;

use App\Decorator\SourceDatabaseAware;
use MongoDB\Database;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use DateTime;

class Inflation implements SourceDatabaseAware
{
    const COLLECTION = 'inflation';
    private Database $database;

    public function get(int $id): ?array
    {
        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return $result ? [
            ...$result,
            'date' => $result['date']
                ? $result['date']->toDateTime()->format('c')
                : null,
        ] : null;
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $item)  {
            return [
                ...$item->getArrayCopy(),
                'date' => $item['date']
                    ? $item['date']->toDateTime()->format('c')
                    : null,
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
        $data = [
            '_id' => $object['id'],
            ...$object,
            ...['date' => $object['date']
                ? new UTCDateTime((new DateTime($object['date']))->getTimestamp() * 1000)
                : null,],
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['id']],
                ['$set' => $data],
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
