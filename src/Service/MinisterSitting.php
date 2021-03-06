<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use function App\{serializeDatesRange, deserializeDatesRange};
use function App\{serializeAssembly, deserializeAssembly};
use function App\{serializeCongressman, deserializeCongressman};
use function App\{serializeParty, deserializeParty};
use function App\{deserializeConstituency, serializeConstituency};
use function App\{deserializeMinistry, serializeMinistry};

class MinisterSitting implements SourceDatabaseAware
{
    const COLLECTION = 'minister-sitting';
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

    public function fetchGovernmentPartiesByAssembly(int $assemblyId)
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
                    '$group' => [
                        '_id' => '$congressman_party.party_id',
                        'party' => ['$push' => '$$ROOT']
                    ]
                ],
                [
                    '$addFields' => [
                        'party' => ['$first' => '$party.congressman_party']
                    ]
                ],
                [
                    '$replaceRoot' => ['newRoot' => '$party']
                ],
                [
                    '$sort' => ['name' => 1]
                ]
            ]);

        return array_map(function (BSONDocument $item) {
            return deserializeParty($item);
        }, iterator_to_array($documents));
    }

    public function fetchGovernmentSessionsByAssembly(int $assemblyId)
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
                    '$group' => [
                        '_id' => '$ministry.ministry_id',
                        'ministry_id' => ['$first' => '$ministry.ministry_id'],
                        'name' => ['$first' => '$ministry.name'],
                        'abbr_short' => ['$first' => '$ministry.abbr_short'],
                        'abbr_long' => ['$first' => '$ministry.abbr_long'],
                        'first' => ['$first' => '$ministry.first'],
                        'last' => ['$first' => '$ministry.last'],
                        'first_ministry_assembly' => ['$first' => '$first_ministry_assembly'],
                        'last_ministry_assembly' => ['$first' => '$last_ministry_assembly'],
                        'congressmen' => ['$push' => '$$ROOT']
                    ]
                ],
                [
                    '$set' => [
                        'congressmen' => [
                            '$function' => [
                                'body' => 'function(all) {
                                    all.sort((a, b) => a.from - b.from)
                                    return all;
                                }',
                                'args' => ['$congressmen'],
                                'lang' => "js"
                            ]
                        ]
                    ]
                ],
            ]);

        return array_map(function (BSONDocument $item) {
            return [
                '_id' => $item['_id'],
                ...deserializeMinistry($item),
                'first_ministry_assembly' => $item['first_ministry_assembly']
                    ? deserializeAssembly($item['first_ministry_assembly'])
                    : null,
                'last_ministry_assembly' => $item['last_ministry_assembly']
                    ? deserializeAssembly($item['last_ministry_assembly'])
                    : null,
                'congressmen' => array_map(function ($congressman) {
                    return [
                        '_id' => $congressman['_id'],
                        'minister_sitting_id' => $congressman['minister_sitting_id'],
                        ...deserializeDatesRange($congressman),
                        'assembly' => $congressman['assembly']
                            ? deserializeAssembly($congressman['assembly'])
                            : null,
                        'congressman' => $congressman['congressman']
                            ? deserializeCongressman($congressman['congressman'])
                            : null,
                        'congressman_constituency' => $congressman['congressman_constituency']
                            ? deserializeConstituency($congressman['congressman_constituency'])
                            : null,
                        'congressman_party' => $congressman['congressman_party']
                            ? deserializeParty($congressman['congressman_party'])
                            : null,
                        'ministry' => $congressman['ministry']
                            ? deserializeMinistry($congressman['ministry'])
                            : null,
                    ];
                }, $item['congressmen']->getArrayCopy()),
            ];
        }, iterator_to_array($documents));
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
}
