<?php

namespace App\Service;

use App\Decorator\SourceDatabaseAware;
use function App\serializeDatesRange;
use function App\deserializeDatesRange;
use function App\serializeBirth;
use function App\deserializeBirth;
use MongoDB\Model\BSONDocument;
use MongoDB\Database;

class CommitteeSitting implements SourceDatabaseAware
{
    const COLLECTION = 'committee-sitting';
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
            'congressman' => $document['congressman'] ? [
                ...$document['congressman'],
                ...deserializeBirth($document['congressman']),
            ] : null,
            'committee' => $document['committee'] ? [
                ...$document['committee'],
            ] : null,
            'congressman_party' => $document['congressman_party'] ? [
                ...$document['congressman_party']
            ] : null,
            'congressman_constituency' => $document['congressman_constituency'] ? [
                ...$document['congressman_constituency']
            ] : null,
            'first_committee_assembly' => $document['first_committee_assembly'] ? [
                ...$document['first_committee_assembly'],
                ...deserializeDatesRange($document['first_committee_assembly']),
            ] : null,
            'last_committee_assembly' => $document['last_committee_assembly'] ? [
                ...$document['last_committee_assembly'],
                ...deserializeDatesRange($document['last_committee_assembly']),
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
                'congressman' => $document['congressman'] ? [
                    ...$document['congressman'],
                    ...deserializeBirth($document['congressman']),
                ] : null,
                'committee' => $document['committee'] ? [
                    ...$document['committee'],
                ] : null,
                'congressman_party' => $document['congressman_party'] ? [
                    ...$document['congressman_party']
                ] : null,
                'congressman_constituency' => $document['congressman_constituency'] ? [
                    ...$document['congressman_constituency']
                ] : null,
                'first_committee_assembly' => $document['first_committee_assembly'] ? [
                    ...$document['first_committee_assembly'],
                    ...deserializeDatesRange($document['first_committee_assembly']),
                ] : null,
                'last_committee_assembly' => $document['last_committee_assembly'] ? [
                    ...$document['last_committee_assembly'],
                    ...deserializeDatesRange($document['last_committee_assembly']),
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
            '_id' => $object['committee_sitting_id'],
            ...$object,
            ...serializeDatesRange($object),
            'assembly' => $object['assembly'] ? [
                ...$object['assembly'],
                ...serializeDatesRange($object['assembly']),
            ] : null,
            'congressman' => $object['congressman'] ? [
                ...$object['congressman'],
                ...serializeBirth($object['congressman']),
            ] : null,
            'committee' => $object['committee'] ? [
                ...$object['committee'],
            ] : null,
            'congressman_party' => $object['congressman_party'] ? [
                ...$object['congressman_party']
            ]: null,
            'congressman_constituency' => $object['congressman_constituency'] ? [
                ...$object['congressman_constituency']
            ]: null,
            'first_committee_assembly' => $object['first_committee_assembly'] ? [
                ...$object['first_committee_assembly'],
                ...serializeDatesRange($object['first_committee_assembly']),
            ]: null,
            'last_committee_assembly' => $object['last_committee_assembly'] ? [
                ...$object['last_committee_assembly'],
                ...serializeDatesRange($object['last_committee_assembly']),
            ]: null,
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['committee_sitting_id']],
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
                ['first_committee_assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['first_committee_assembly' => [
                    ...$assembly,
                    ...serializeDatesRange($assembly),
                ]]],
                ['upsert' => false]
            );

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['last_committee_assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['last_committee_assembly' => [
                    ...$assembly,
                    ...serializeDatesRange($assembly),
                ]]],
                ['upsert' => false]
            );
    }

    public function updateCommittee($committee): void
    {
        if (!$committee) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['committee.committee_id' => $committee['committee_id']],
                ['$set' => ['committee' => [
                    ...$committee,
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

    public function updateParty($party): void
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

    public function updateConstituency($constituency): void
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
