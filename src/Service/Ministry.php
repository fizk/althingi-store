<?php

namespace App\Service;

use App\Decorator\SourceDatabaseAware;
use MongoDB\Database;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use DateTime;

class Ministry implements SourceDatabaseAware
{
    const COLLECTION = 'ministry';
    private Database $database;

    public function get(int $id): ?array
    {
        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        if (!$result) {
            return null;
        }


        $record = $result->getArrayCopy();
        return [
            ...$record,
            'first' => $this->assembly($record['first']),
            'last' => $this->assembly($record['last']),
        ];
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document) {
            $record = $document->getArrayCopy();
            return [
                ...$record,
                'first' => $this->assembly($record['first']),
                'last' => $this->assembly($record['last']),
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
            '_id' => $object['ministry_id'],
            ...$object,
            'first' => $object['first'] ? [
                ...$object['first'],
                'from' => $object['first']['from']
                    ? new UTCDateTime((new DateTime($object['first']['from']))->getTimestamp() * 1000)
                    : null,
                'to' => $object['first']['to']
                    ? new UTCDateTime((new DateTime($object['first']['to']))->getTimestamp() * 1000)
                    : null,
            ] : null,
            'last' => $object['last'] ? [
                ...$object['last'],
                'from' => $object['last']['from']
                    ? new UTCDateTime((new DateTime($object['last']['from']))->getTimestamp() * 1000)
                    : null,
                'to' => $object['last']['to']
                    ? new UTCDateTime((new DateTime($object['last']['to']))->getTimestamp() * 1000)
                    : null,
            ] : null,
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['ministry_id']],
                ['$set' => $data],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }

    public function updateAssembly(?array $assembly)
    {
        if (!$assembly) {
            return null;
        }

        $firstResult = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['first.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['first' => [
                    ...$assembly,
                    'from' => $assembly['from']
                        ? new UTCDateTime((new DateTime($assembly['from']))->getTimestamp() * 1000)
                        : null,
                    'to' => $assembly['to']
                        ? new UTCDateTime((new DateTime($assembly['to']))->getTimestamp() * 1000)
                        : null,
                ]]],
                ['upsert' => false]
            );

        $lastResult = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['last.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['last' => [
                    ...$assembly,
                    'from' => $assembly['from']
                        ? new UTCDateTime((new DateTime($assembly['from']))->getTimestamp() * 1000)
                        : null,
                    'to' => $assembly['to']
                        ? new UTCDateTime((new DateTime($assembly['to']))->getTimestamp() * 1000)
                        : null,
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

    private function assembly(?BSONDocument $assembly)
    {
        if (!$assembly) {
            return null;
        }

        $record = $assembly->getArrayCopy();
        return [
            ...$record,
            'from' => $record['from']
                ? $record['from']->toDateTime()->format('c')
                : null,
            'to' => $record['to']
                ? $record['to']->toDateTime()->format('c')
                : null,
        ];
    }
}
