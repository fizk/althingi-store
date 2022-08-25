<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use App\Presenter\{
    AssemblyConstituenciesSittingPresenter,
    AssemblyCongressmanSittingPresenter,
    AssemblyPartySittingPresenter,
    AssemblyPresenter,
    CongressmanPresenter,
    CongressmanSittingPresenter,
    ConstituencyPresenter,
    PartyPresenter
};
use MongoDB\Model\BSONDocument;

class CongressmanSitting implements SourceDatabaseAware
{
    const COLLECTION = 'congressman-sitting';
    use SourceDatabaseTrait;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return (new CongressmanSittingPresenter)->unserialize($document);
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document)  {
            return (new CongressmanSittingPresenter)->unserialize($document);
        }, iterator_to_array(
            $this->getSourceDatabase()->selectCollection(self::COLLECTION)->find()
        ));
    }

    public function getCongressmanAndAssembly(int $assemblyId, int $congressmanId): ?array
    {
        /** @var \MongoDB\Driver\Cursor */
        $documents = $this->getSourceDatabase()->selectCollection(self::COLLECTION)->aggregate([
            [
                '$match' => [
                    'assembly.assembly_id' => $assemblyId,
                    'congressman.congressman_id' => $congressmanId
                ]
            ],
            [
                '$project' => [
                    'assembly' => 1,
                    'congressman' => 1
                ]
            ],
            [
                '$limit' => 1
            ]
        ]);

        $documents->rewind();
        $document = $documents->current();

        if (!$document) {
            return null;
        }

        return [
            'assembly' => (new AssemblyPresenter)->unserialize($document['assembly']),
            'congressman' => (new CongressmanPresenter)->unserialize($document['congressman'])
        ];
    }

    public function fetchPartiesByAssembly(int $assemblyId)
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
                        'party' => ['$first' => '$congressman_party']
                    ]
                ],
                [
                    '$replaceRoot' => ['newRoot' => '$party']
                ]
        ]);

        return array_map(function (BSONDocument $item) {
            return (new PartyPresenter)->unserialize($item);
        },iterator_to_array($documents));
    }

    public function fetchCongressmenSessions(int $assemblyId, bool $primary = true)
    {
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => [
                        'assembly.assembly_id' => $assemblyId,
                        'type' => $primary ? ['$ne' => 'varamaður'] : ['$eq' => 'varamaður']
                    ]
                ],
                [
                    '$group' => [
                        '_id' => '$congressman.congressman_id',
                        'sessions' => ['$push' => '$$ROOT']
                    ]
                ],
                [
                    '$set' => [
                        "sessions" => [
                            '$sortArray' => [
                                'input' => '$sessions',
                                'sortBy' => ['from' => 1]
                            ]
                        ]
                    ]
                ],
                [
                    '$addFields' => [
                        'congressman' => [
                            '$first' => '$sessions.congressman'
                        ],
                        'assembly' => [
                            '$first' => '$sessions.assembly'
                        ]
                    ]
                ],
                [
                    '$sort' => ['congressman.name' => 1]
                ]
            ]
        );

        return array_map(function (BSONDocument $document) {
            return (new AssemblyCongressmanSittingPresenter)->unserialize($document);
        }, iterator_to_array($documents));
    }

    public function fetchConstituenciesSessions(int $assemblyId, bool $primary = true)
    {
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => [
                        'assembly.assembly_id' => $assemblyId,
                        'type' => $primary ? ['$ne' => 'varamaður'] : ['$eq' => 'varamaður']
                    ]
                ],
                [
                    '$group' => [
                        '_id' => [
                            'congressman' => '$congressman.congressman_id',
                            'constituency' => '$congressman_constituency.constituency_id'
                        ],
                        'congressman' => ['$first' => '$congressman'],
                        'assembly' => ['$first' => '$assembly'],
                        'sessions' => [
                            '$push' => [
                                '_id' => '$_id',
                                'congressman_party' => '$congressman_party',
                                'congressman_constituency' => '$congressman_constituency',
                                'from' => '$from',
                                'to' => '$to',
                                'type' => '$type'
                            ]
                        ],
                        'congressman_constituency' => ['$first' => '$congressman_constituency']
                    ]
                ],
                [

                    '$group' => [
                        '_id' => '$_id.constituency',

                        'congressmen' => ['$push' => '$$ROOT']
                    ]
                ],
                [
                    '$addFields' => [
                        'assembly' => ['$first' => '$congressmen.assembly'],
                        'constituency_id' => ['$first' => '$congressmen.congressman_constituency.constituency_id'],
                        'name' => ['$first' => '$congressmen.congressman_constituency.name'],
                        'abbr_short' => ['$first' => '$congressmen.congressman_constituency.abbr_short'],
                        'abbr_long' => ['$first' => '$congressmen.congressman_constituency.abbr_long'],
                        'description' => ['$first' => '$congressmen.congressman_constituency.description'],
                    ]
                ],
                [
                    '$set' => [
                        'congressmen' => [
                            '$function' => [
                                'body' => 'function(all) {' .
                                'all.sort((a, b) => a.congressman.name.localeCompare(b.congressman.name, "is"));' .
                                'return all;' .
                                '}',
                                'args' => ['$congressmen'],
                                'lang' => 'js'
                            ]
                        ]
                    ]
                ],
                [
                    '$sort' => ['name' => 1]
                ]
            ]);

        return array_map(function(BSONDocument $document){
            return (new AssemblyConstituenciesSittingPresenter)->unserialize($document);
        }, iterator_to_array($documents));
    }

    public function fetchPartiesSessions(int $assemblyId, bool $primary = true)
    {
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => [
                        'assembly.assembly_id' => $assemblyId,
                        'type' => $primary ? ['$ne' => 'varamaður'] : ['$eq' => 'varamaður']
                    ]
                ],
                [
                    '$sort' => [
                        'congressman.name' => 1
                    ]
                ],
                [
                    '$group' => [
                        '_id' => [
                            'congressman' => '$congressman.congressman_id',
                            'party' => '$congressman_party.party_id'
                        ],
                        'congressman' => ['$first' => '$congressman'],
                        'assembly' => ['$first' => '$assembly'],
                        'sessions' => [
                            '$push' => [
                                '_id' => '$_id',
                                'congressman_party' => '$congressman_party',
                                'congressman_constituency' => '$congressman_constituency',
                                'from' => '$from',
                                'to' => '$to',
                                'type' => '$type'
                            ]
                        ],
                        'congressman_party' => ['$first' => '$congressman_party']
                    ]
                ],
                [

                    '$group' => [
                        '_id' => '$_id.party',

                        'congressmen' => ['$push' => '$$ROOT']
                    ]
                ],
                [
                    '$addFields' => [
                        'assembly' => ['$first' => '$congressmen.assembly'],
                        'congressman_constituency' => ['$first' => '$congressmen.congressman_constituency'],
                        'party_id' => ['$first' => '$congressmen.congressman_party.party_id'],
                        'name' => ['$first' => '$congressmen.congressman_party.name'],
                        'abbr_short' => ['$first' => '$congressmen.congressman_party.abbr_short'],
                        'abbr_long' => ['$first' => '$congressmen.congressman_party.abbr_long'],
                        'color' => ['$first' => '$congressmen.congressman_party.color'],
                    ]
                ],
                [
                    '$set' => [
                        'congressmen' => [
                            '$function' => [
                                'body' => 'function(all) {'.
                                    'all.sort((a, b) => a.congressman.name.localeCompare(b.congressman.name, "is"));'.
                                    'return all;'.
                                '}',
                                'args' => ['$congressmen'],
                                'lang' => 'js'
                            ]
                        ]
                    ]
                ],
                [
                    '$sort' => ['name' => 1]
                ]
            ]
        );

        return array_map(function (BSONDocument $item) {
            return (new AssemblyPartySittingPresenter())->unserialize($item);
        }, iterator_to_array($documents));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = (new CongressmanSittingPresenter)->serialize($object);

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $document['session_id']],
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
                ['$set' => ['assembly' => (new AssemblyPresenter)->serialize($assembly)]],
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
                ['$set' => ['congressman_party' => (new PartyPresenter)->serialize($party)]],
                ['upsert' => false]
            );
    }

    public function updateCongressman(?array $congressman): void
    {
        if (!$congressman) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['congressman.congressman_id' => $congressman['congressman_id']],
                ['$set' => ['congressman' => (new CongressmanPresenter)->serialize($congressman),]],
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
                ['$set' => ['congressman_constituency' => (new ConstituencyPresenter)->serialize($constituency),]],
                ['upsert' => false]
            );
    }
}
