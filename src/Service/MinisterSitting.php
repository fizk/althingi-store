<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use App\Presenter\{
    AssemblyMinisterSittingPresenter,
    AssemblyPresenter,
    CongressmanPresenter,
    ConstituencyPresenter,
    MinisterSittingPresenter,
    MinistryPresenter,
    PartyPresenter
};
use MongoDB\Model\BSONDocument;

class MinisterSitting implements SourceDatabaseAware
{
    const COLLECTION = 'minister-sitting';
    use SourceDatabaseTrait;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return (new MinisterSittingPresenter)->unserialize($document);
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document) {
            return (new MinisterSittingPresenter)->unserialize($document);
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
            return (new PartyPresenter)->unserialize($item);
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

        return array_map(function (BSONDocument $document) {
            return (new AssemblyMinisterSittingPresenter)->unserialize($document);
        }, iterator_to_array($documents));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = (new MinisterSittingPresenter)->serialize($object);

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $document['minister_sitting_id']],
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
                ['$set' => ['assembly' => (new AssemblyPresenter)
                    ->serialize($assembly)]],
                ['upsert' => false]
            );

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['first_ministry_assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['first_ministry_assembly' => (new AssemblyPresenter)
                    ->serialize($assembly)]],
                ['upsert' => false]
            );

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['last_ministry_assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['last_ministry_assembly' => (new AssemblyPresenter)
                    ->serialize($assembly)]],
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

    public function updateConstituency(?array $constituency): void
    {
        if (!$constituency) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['congressman_constituency.constituency_id' => $constituency['constituency_id']],
                ['$set' => ['congressman_constituency' => (new ConstituencyPresenter)->serialize($constituency)]],
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
                ['$set' => ['ministry' => (new MinistryPresenter)->serialize($ministry)]],
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
                ['$set' => ['congressman' => (new CongressmanPresenter)->serialize($congressman)]],
                ['upsert' => false]
            );
    }
}
