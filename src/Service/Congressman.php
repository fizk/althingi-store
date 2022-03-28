<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use function App\{
    serializeCongressman,
    deserializeCongressman
};

class Congressman implements SourceDatabaseAware
{
    const COLLECTION = 'congressman';
    use SourceDatabaseTrait;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return $document
            ? deserializeCongressman($document)
            : null;
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document)  {
            return deserializeCongressman($document);
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
            '_id' => $object['congressman_id'],
            ...serializeCongressman($object),
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['congressman_id']],
                ['$set' => $document],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }
}
