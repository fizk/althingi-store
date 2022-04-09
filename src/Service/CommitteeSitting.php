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

    public function fetchByAssembly(int $assemblyId): array
    {
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => [
                        'assembly.assembly_id' => $assemblyId
                    ]
                ],
                [
                    '$project' => [
                        '_id' => 1,
                        'assembly' => 1,
                        'committee' => 1,
                        'committee_sitting_id' => 1,
                        'congressman' => 1,
                        'congressman_constituency' => 1,
                        'congressman_party' => 1,
                        'first_committee_assembly' => 1,
                        'from' => 1,
                        'last_committee_assembly' => 1,
                        'type' => '$role',
                        'order' => 1,
                        'to' => 1,
                    ]
                ],
                [
                    '$group' => [
                        '_id' => [
                            'congressman' => '$congressman.congressman_id',
                            'committee' => '$committee.committee_id'
                        ],
                        'id' => ['$first' => '$_id'],
                        'committee' => ['$first' => '$committee'],
                        'congressman' => ['$first' => '$congressman'],
                        'assembly' => ['$first' => '$assembly'],
                        'first_assembly' => ['$first' => '$first_committee_assembly'],
                        'last_assembly' => ['$first' => '$last_committee_assembly'],
                        'sessions' => ['$push' => '$$ROOT']
                    ]
                ],
                [
                    '$group' => [
                        '_id' => '$_id.committee',
                        'committee_id' => ['$first' => '$committee.committee_id'],
                        'name' => ['$first' => '$committee.name'],
                        'first_assembly_id' => ['$first' => '$committee.first_assembly_id'],
                        'last_assembly_id' => ['$first' => '$committee.last_assembly_id'],
                        'abbr_long' => ['$first' => '$committee.abbr_long'],
                        'abbr_short' => ['$first' => '$committee.abbr_short'],
                        'assembly' => ['$first' => '$assembly'],
                        'first_assembly' => ['$first' => '$first_assembly'],
                        'last_assembly' => ['$first' => '$last_assembly'],
                        'sessions' => [
                            '$push' => [
                                '_id' => '$id',
                                'congressman' => '$$ROOT.congressman',
                                'assembly' => '$$ROOT.assembly',
                                'sessions' => '$$ROOT.sessions'
                            ],
                        ]
                    ]
                ],
                [
                    '$sort' => ['committee.name' => 1]
                ]
            ]);

        return array_map(function (BSONDocument $item) {
            return [
                ...$item,
                'assembly' => $item['assembly']
                    ? deserializeAssembly($item['assembly'])
                    : null,
                'first_assembly' => $item['first_assembly']
                    ? deserializeAssembly($item['first_assembly'])
                    : null,
                'last_assembly' => $item['last_assembly']
                    ? deserializeAssembly($item['last_assembly'])
                    : null,
                'sessions' => array_map(function (BSONDocument $session) {
                    return [
                        ...$session,
                        'congressman' => $session['congressman']
                            ? deserializeCongressman($session['congressman']) : null,
                        'assembly' => $session['assembly']
                            ? deserializeAssembly($session['assembly']) : null,
                        'sessions' => array_map(function (BSONDocument $document) {
                            return [
                                '_id' => $document['_id'],
                                ...deserializeDatesRange($document),
                                'assembly' => $document['assembly']
                                    ? deserializeAssembly($document['assembly'])
                                    : null,
                                'congressman_party' => $document['congressman_party']
                                    ? deserializeParty($document['congressman_party'])
                                    : null,
                                'congressman_constituency' => $document['congressman_constituency']
                                    ? deserializeConstituency($document['congressman_constituency'])
                                    : null,
                                'abbr' => null,
                                'type' => $document['type'],
                                'order' => $document['order'],
                            ];
                        }, $session['sessions']->getArrayCopy())
                    ];
                }, $item['sessions']->getArrayCopy()),

            ];
        }, iterator_to_array($documents));
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
