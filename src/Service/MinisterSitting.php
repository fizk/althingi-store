<?php

namespace App\Service;

use App\Decorator\SourceDatabaseAware;
use MongoDB\Database;
use MongoDB\Model\BSONDocument;
use function App\{serializeDatesRange, deserializeDatesRange};
use function App\{serializeAssembly, deserializeAssembly};
use function App\{serializeCongressman, deserializeCongressman};
use function App\{serializeParty, deserializeParty};
use function App\{deserializeConstituency, serializeConstituency};
use function App\{deserializeMinistry, serializeMinistry};

class MinisterSitting implements SourceDatabaseAware
{
    const COLLECTION = 'minister-sitting';
    private Database $database;

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
            'ministry' => $document['ministry']
                ? deserializeMinistry($document['ministry'])
                : null,
            'congressman' => $document['congressman']
                ? deserializeCongressman($document['congressman'])
                : null,
            'congressman_party' => $document['congressman_party']
                ? deserializeParty($document['congressman_party'])
                : null,
            'congressman_constituency' => $document['congressman_constituency']
                ? deserializeConstituency($document['congressman_constituency'])
                : null,
            'first_ministry_assembly' => $document['first_ministry_assembly']
                ? deserializeAssembly($document['first_ministry_assembly'])
                : null,
            'last_ministry_assembly' => $document['last_ministry_assembly']
                ? deserializeAssembly($document['last_ministry_assembly'])
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
                'ministry' => $document['ministry']
                    ? deserializeMinistry($document['ministry'])
                    : null,
                'congressman' => $document['congressman']
                    ? deserializeCongressman($document['congressman'])
                    : null,
                'congressman_party' => $document['congressman_party']
                    ? deserializeParty($document['congressman_party'])
                    : null,
                'congressman_constituency' => $document['congressman_constituency']
                    ? deserializeConstituency($document['congressman_constituency'])
                    : null,
                'first_ministry_assembly' => $document['first_ministry_assembly']
                    ? deserializeAssembly($document['first_ministry_assembly'])
                    : null,
                'last_ministry_assembly' => $document['last_ministry_assembly']
                    ? deserializeAssembly($document['last_ministry_assembly'])
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
            '_id' => $object['minister_sitting_id'],
            ...$object,
            ...serializeDatesRange($object),
            'assembly' => $object['assembly']
                ? serializeAssembly($object['assembly'])
                : null,
            'ministry' => $object['ministry'] ? [
                ...$object['ministry'],
            ] : null,
            'congressman' => $object['congressman']
                ? serializeCongressman($object['congressman'])
                : null,
            'congressman_constituency' => $object['congressman_constituency']
                ? serializeConstituency($object['congressman_constituency'])
                : null,
            'congressman_party' => $object['congressman_party']
                ? serializeParty($object['congressman_party'])
                : null,
            'first_ministry_assembly' => $object['first_ministry_assembly']
                ? serializeAssembly($object['first_ministry_assembly'])
                : null,
            'last_ministry_assembly' => $object['last_ministry_assembly']
                ? serializeAssembly($object['last_ministry_assembly'])
                : null,
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['minister_sitting_id']],
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
                ['first_ministry_assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['first_ministry_assembly' => serializeAssembly($assembly)]],
                ['upsert' => false]
            );

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['last_ministry_assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['last_ministry_assembly' => serializeAssembly($assembly)]],
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
                ['$set' => ['congressman_party' => serializeParty($party)]],
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
                ['$set' => ['congressman_constituency' => serializeConstituency($constituency)]],
                ['upsert' => false]
            );
    }

    public function updateMinistry(?array $ministry)
    {
        if (!$ministry) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['ministry.ministry_id' => $ministry['ministry_id']],
                ['$set' => ['ministry' => serializeMinistry($ministry)]],
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
