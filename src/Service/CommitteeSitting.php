<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use App\Presenter\{
    AssemblyPresenter,
    AssemblyCommitteeSittingPresenter,
    CommitteePresenter,
    CommitteeSittingPresenter,
    CongressmanPresenter,
    ConstituencyPresenter,
    PartyPresenter
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

        return (new CommitteeSittingPresenter)->unserialize($document);
    }

    public function fetch(): array
    {
        $documents = $this->getSourceDatabase()->selectCollection(self::COLLECTION)->aggregate([
            [
                '$sort' => ['session_id' => 1]
            ]
        ]);
        return array_map(function (BSONDocument $document) {
            return (new CommitteeSittingPresenter)->unserialize($document);
        }, iterator_to_array($documents));
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
                                "sessions" => [
                                    '$sortArray' => [
                                        'input' => '$$ROOT.sessions',
                                        'sortBy' => [ 'from' => 1 ]
                                    ]
                                ]
                            ],
                        ]
                    ]
                ],
                [
                    '$set' => [
                            "sessions" => [
                                    '$sortArray' => [
                                        'input' => '$sessions',
                                        'sortBy' => ["congressman.name" => 1]
                                    ]
                            ]
                    ]
                ],
                [
                    '$sort' => ['name' => 1]
                ]
            ]);

        return array_map(function (BSONDocument $item) {
            return (new AssemblyCommitteeSittingPresenter)->unserialize($item);
        }, iterator_to_array($documents));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = (new CommitteeSittingPresenter)->serialize($object);

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $document['committee_sitting_id']],
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

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['first_committee_assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['first_committee_assembly' => (new AssemblyPresenter)->serialize($assembly)]],
                ['upsert' => false]
            );

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['last_committee_assembly.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['last_committee_assembly' => (new AssemblyPresenter)->serialize($assembly)]],
                ['upsert' => false]
            );
    }

    public function updateCommittee(?array $committee): void
    {
        if (!$committee) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['committee.committee_id' => $committee['committee_id']],
                ['$set' => ['committee' => (new CommitteePresenter)->serialize($committee)]],
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
                ['$set' => ['congressman' => (new CongressmanPresenter)->serialize($congressman)]],
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
}
