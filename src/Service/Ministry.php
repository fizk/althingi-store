<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use App\Presenter\{
    MinistryPresenter,
    AssemblyPresenter
};
use MongoDB\Model\BSONDocument;

class Ministry implements SourceDatabaseAware
{
    const COLLECTION = 'ministry';
    use SourceDatabaseTrait;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return (new MinistryPresenter)->unserialize($document);
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document) {
            return (new MinistryPresenter)->unserialize($document);
        }, iterator_to_array(
            $this->getSourceDatabase()->selectCollection(self::COLLECTION)->find()
        ));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = (new MinistryPresenter)->serialize($object);

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $document['ministry_id']],
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
                ['first.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['first' => (new AssemblyPresenter)->serialize($assembly)]],
                ['upsert' => false]
            );

        $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateMany(
                ['last.assembly_id' => $assembly['assembly_id']],
                ['$set' => ['last' => (new AssemblyPresenter)->serialize($assembly)]],
                ['upsert' => false]
            );

    }
}
