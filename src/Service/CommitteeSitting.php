<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use function App\{
    serializeDatesRange,
    deserializeDatesRange,
    serializeAssembly,
    deserializeAssembly,
    serializeCongressman,
    deserializeCongressman,
    serializeCommittee,
    deserializeCommittee,
    serializeParty,
    deserializeParty,
    serializeConstituency,
    deserializeConstituency
};
use MongoDB\Model\BSONDocument;

/**
 * @todo there is no call to updateCongressman method
 *  because there is no CongressmanController (currently).
 */
class CommitteeSitting implements SourceDatabaseAware
{
    const COLLECTION = 'committee-sitting';
    use SourceDatabaseTrait;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return $document ? [
            ...$document,
            ...deserializeDatesRange($document),
            'assembly' => $document['assembly']
                ? deserializeAssembly($document['assembly'])
                : null,
            'congressman' => $document['congressman']
                ? deserializeCongressman($document['congressman'])
                : null,
            'committee' => $document['committee']
                ? deserializeCommittee($document['committee'])
                : null,
            'congressman_party' => $document['congressman_party']
                ? deserializeParty($document['congressman_party'])
                : null,
            'congressman_constituency' => $document['congressman_constituency']
                ? deserializeConstituency($document['congressman_constituency'])
                : null,
            'first_committee_assembly' => $document['first_committee_assembly']
                ? deserializeAssembly($document['first_committee_assembly'])
                : null,
            'last_committee_assembly' => $document['last_committee_assembly']
                ? deserializeAssembly($document['last_committee_assembly'])
                : null,
        ] : null;
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document) {
            return [
                ...$document,
                ...deserializeDatesRange($document),
                'assembly' => $document['assembly']
                    ? deserializeAssembly($document['assembly'])
                    : null,
                'congressman' => $document['congressman']
                    ? deserializeCongressman($document['congressman'])
                    : null,
                'committee' => $document['committee']
                    ? deserializeCommittee($document['committee'])
                    : null,
                'congressman_party' => $document['congressman_party']
                    ? deserializeParty($document['congressman_party'])
                    : null,
                'congressman_constituency' => $document['congressman_constituency']
                    ? deserializeConstituency($document['congressman_constituency'])
                    : null,
                'first_committee_assembly' => $document['first_committee_assembly']
                    ? deserializeAssembly($document['first_committee_assembly'])
                    : null,
                'last_committee_assembly' => $document['last_committee_assembly']
                    ? deserializeAssembly($document['last_committee_assembly'])
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
        $document = [
            '_id' => $object['committee_sitting_id'],
            ...$object,
            ...serializeDatesRange($object),
            'assembly' => $object['assembly']
                ? serializeAssembly($object['assembly'])
                : null,
            'congressman' => $object['congressman']
                ? serializeCongressman($object['congressman'])
                : null,
            'committee' => $object['committee']
                ? serializeCommittee($object['committee'])
                : null,
            'congressman_party' => $object['congressman_party']
                ? serializeParty($object['congressman_party'])
                : null,
            'congressman_constituency' => $object['congressman_constituency']
                ? serializeConstituency($object['congressman_constituency'])
                : null,
            'first_committee_assembly' => $object['first_committee_assembly']
                ? serializeAssembly($object['first_committee_assembly'])
                : null,
            'last_committee_assembly' => $object['last_committee_assembly']
                ? serializeAssembly($object['last_committee_assembly'])
                : null,
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
                ['$set' => ['assembly' => serializeAssembly($assembly)]],
                ['upsert' => false]
            );

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['first_committee_assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['first_committee_assembly' => serializeAssembly($assembly)]],
                ['upsert' => false]
            );

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['last_committee_assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['last_committee_assembly' => serializeAssembly($assembly)]],
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
                ['$set' => ['committee' => serializeCommittee($committee)]],
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
                ['$set' => ['congressman' => serializeCongressman($congressman)]],
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
                ['$set' => ['congressman_party' => serializeParty($party)]],
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
                ['$set' => ['congressman_constituency' => serializeConstituency($constituency)]],
                ['upsert' => false]
            );
    }
}
