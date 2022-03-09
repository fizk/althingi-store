<?php

namespace App\Service;

use App\Decorator\SourceDatabaseAware;
use MongoDB\Database;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use DateTime;

class Committee implements SourceDatabaseAware
{
    const COLLECTION = 'committee';
    private Database $database;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return $document
            ? [
                ...$document,
                'first' => $document['first'] ? [
                    ...$document['first'],
                    'from' => $document['first']['from']
                        ? $document['first']['from']->toDateTime()->format('c')
                        : null,
                    'to' => $document['first']['to']
                        ? $document['first']['to']->toDateTime()->format('c')
                        : null,
                ] : null,
                'last' => $document['last'] ? [
                    ...$document['last'],
                    'from' => $document['last']['from']
                        ? $document['last']['from']->toDateTime()->format('c')
                        : null,
                    'to' => $document['last']['to']
                        ? $document['last']['to']->toDateTime()->format('c')
                        : null,
                ] : null,
            ]
            : null;
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document)  {
            return [
                ...$document,
                'first' => $document['first'] ? [
                    ...$document['first'],
                    'from' => $document['first']['from']
                        ? $document['first']['from']->toDateTime()->format('c')
                        : null,
                    'to' => $document['first']['to']
                        ? $document['first']['to']->toDateTime()->format('c')
                        : null,
                ] : null,
                'last' => $document['last'] ? [
                    ...$document['last'],
                    'from' => $document['last']['from']
                        ? $document['last']['from']->toDateTime()->format('c')
                        : null,
                    'to' => $document['last']['to']
                        ? $document['last']['to']->toDateTime()->format('c')
                        : null,
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
        $data = [
            '_id' => $object['committee_id'],
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
                ['_id' => $object['committee_id']],
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
            ->updateMany(
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
            ->updateMany(
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
}
