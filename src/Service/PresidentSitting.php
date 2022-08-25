<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use App\Presenter\{
    AssemblyPresidentSittingPresenter,
    AssemblyPresenter,
    CongressmanPresenter,
    ConstituencyPresenter,
    PartyPresenter,
    PresidentSittingPresenter
};
use MongoDB\Model\BSONDocument;

class PresidentSitting implements SourceDatabaseAware
{
    const COLLECTION = 'president-sitting';
    use SourceDatabaseTrait;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return (new PresidentSittingPresenter)->unserialize($document);
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document) {
            return (new PresidentSittingPresenter)->unserialize($document);
        }, iterator_to_array(
            $this->getSourceDatabase()->selectCollection(self::COLLECTION)->find()
        ));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = (new PresidentSittingPresenter)->serialize($object);

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $document['_id']],
                ['$set' => $document],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }

    public function fetchByAssembly(int $assemblyId): array
    {
        return array_map(function (BSONDocument $document) {
            return (new AssemblyPresidentSittingPresenter)->unserialize($document);
        }, iterator_to_array(
            $this->getSourceDatabase()->selectCollection(self::COLLECTION)->aggregate([[
                '$match' => [
                        'assembly._id' => $assemblyId
                    ]
                ],
                [
                    '$project' => [
                        '_id' => '$_id',
                        'congressman' => '$congressman',
                        'assembly' => '$assembly',
                        'sessions' => [[
                            '_id' => '$_id',
                            'abbr' => '$abbr',
                            'congressman_party' => '$congressman_party',
                            'congressman_constituency' => '$congressman_constituency',
                            'from' => '$from',
                            'to' => '$to',
                            'type' => '$title'
                        ]]
                    ]
                ]
            ])
        ));
    }

    public function updateAssembly(?array $assembly): void
    {
        if (!$assembly) {
            return;
        }

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['assembly._id' => $assembly['assembly_id']],
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
                ['congressman_party._id' => $party['party_id']],
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
                ['congressman._id' => $congressman['congressman_id']],
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
                ['congressman_constituency._id' => $constituency['constituency_id']],
                ['$set' => ['congressman_constituency' => (new ConstituencyPresenter)->serialize($constituency),]],
                ['upsert' => false]
            );
    }
}
