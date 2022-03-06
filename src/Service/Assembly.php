<?php

namespace App\Service;

use App\Decorator\SourceDatabaseAware;
use MongoDB\Database;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use DateTime;

class Assembly implements SourceDatabaseAware
{
    const COLLECTION = 'assembly';
    private Database $database;

    public function get(int $id): ?array
    {
        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return $result ? [
            ...$result,
            'from' => $result['from']
                ? $result['from']->toDateTime()->format('c')
                : null,
            'to' => $result['to']
                ? $result['to']->toDateTime()->format('c')
                : null,
        ] : null;
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $item)  {
            return [
                ...$item->getArrayCopy(),
                'from' => $item['from']
                    ? $item['from']->toDateTime()->format('c')
                    : null,
                'to' => $item['to']
                ? $item['to']->toDateTime()->format('c')
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
            '_id' => $object['assembly_id'],
            ...$object,
            ...['from' => $object['from']
                ? new UTCDateTime((new DateTime($object['from']))->getTimestamp() * 1000)
                : null,],
            ...['to' => $object['to']
                ? new UTCDateTime((new DateTime($object['to']))->getTimestamp() * 1000)
                : null]
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['assembly_id']],
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
