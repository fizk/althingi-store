<?php

namespace App\Service;

use App\Decorator\SourceDatabaseAware;
use MongoDB\Database;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use DateTime;

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
            'from' => $document['from']
                ? $document['from']->toDateTime()->format('c')
                : null,
            'to' => $document['to']
                ? $document['to']->toDateTime()->format('c')
                : null,
            'assembly' => $document['assembly'] ? [
                ...$document['assembly'],
                'from' => $document['assembly']['from']
                    ? $document['assembly']['from']->toDateTime()->format('c')
                    : null,
                'to' => $document['assembly']['to']
                    ? $document['assembly']['to']->toDateTime()->format('c')
                    : null,
            ] : null,
            'congressman' => $document['congressman'] ? [
                ...$document['congressman'],
                'birth' => $document['congressman']['birth']
                    ? $document['congressman']['birth']->toDateTime()->format('c')
                    : null,
            ] : null,
            'constituency' => $document['constituency'] ? [
                ...$document['constituency']
            ] : null,
            'party' => $document['party'] ? [
                ...$document['party'],
            ] : null
        ] : null;
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document)  {
            return [
                ...$document,
                'from' => $document['from']
                    ? $document['from']->toDateTime()->format('c')
                    : null,
                'to' => $document['to']
                    ? $document['to']->toDateTime()->format('c')
                    : null,
                'assembly' => $document['assembly'] ? [
                    ...$document['assembly'],
                    'from' => $document['assembly']['from']
                        ? $document['assembly']['from']->toDateTime()->format('c')
                        : null,
                    'to' => $document['assembly']['to']
                        ? $document['assembly']['to']->toDateTime()->format('c')
                        : null,
                ] : null,
                'congressman' => $document['congressman'] ? [
                    ...$document['congressman'],
                    'birth' => $document['congressman']['birth']
                        ? $document['congressman']['birth']->toDateTime()->format('c')
                        : null,
                ] : null,
                'constituency' => $document['constituency'] ? [
                    ...$document['constituency']
                ] : null,
                'party' => $document['party'] ? [
                    ...$document['party'],
                ] : null
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
            ...$object,
            '_id' => $object['session_id'],
            'from' => $object['from']
                ? new UTCDateTime((new DateTime($object['from']))->getTimestamp() * 1000)
                : null,
            'to' => $object['to']
                ? new UTCDateTime((new DateTime($object['to']))->getTimestamp() * 1000)
                : null,
            'assembly' => $object['assembly'] ? [
                ...$object['assembly'],
                'from' => $object['assembly']['from']
                    ? new UTCDateTime((new DateTime($object['assembly']['from']))->getTimestamp() * 1000)
                    : null,
                'to' => $object['assembly']['to']
                    ? new UTCDateTime((new DateTime($object['assembly']['to']))->getTimestamp() * 1000)
                    : null,
            ] : null,
            'congressman' => $object['congressman'] ? [
                ...$object['congressman'],
                'birth' => $object['congressman']['birth']
                    ? new UTCDateTime((new DateTime($object['congressman']['birth']))->getTimestamp() * 1000)
                    : null,
            ] : null,
            'constituency' => $object['constituency'] ? [
                ...$object['constituency']
            ] : null,
            'party' => $object['party'] ? [
                ...$object['party'],
            ] : null
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['session_id']],
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

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['assembly' => [
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

    public function updateParty(?array $party)
    {
        if (!$party) {
            return null;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['party.party_id' => $party['party_id']],
                ['$set' => ['party' => $party,]],
                ['upsert' => false]
            );
    }

    public function updateConstituency(?array $constituency)
    {
        if (!$constituency) {
            return null;
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
