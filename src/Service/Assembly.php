<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use App\Presenter\AssemblyPresenter;
use MongoDB\Model\BSONDocument;

class Assembly implements SourceDatabaseAware
{
    const COLLECTION = 'assembly';
    use SourceDatabaseTrait;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return (new AssemblyPresenter())->unserialize($document);
    }

    public function fetch(): array
    {
        $documents = $this->getSourceDatabase()->selectCollection(self::COLLECTION)->aggregate([
            [
                '$sort' => [
                    'assembly_id' => -1
                ]
            ]
        ]);

        return array_map(function (BSONDocument $document)  {
            return (new AssemblyPresenter())->unserialize($document);
        }, iterator_to_array($documents));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = (new AssemblyPresenter())->serialize($object);

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $document['assembly_id']],
                ['$set' => $document],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }
}
