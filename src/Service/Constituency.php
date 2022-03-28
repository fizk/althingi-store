<?php

namespace App\Service;

use MongoDB\Model\BSONDocument;
use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use function App\{
    serializeConstituency,
    deserializeConstituency
};

class Constituency implements SourceDatabaseAware
{
    const COLLECTION = 'constituency';
    use SourceDatabaseTrait;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return $document
            ? deserializeConstituency($document)
            : null;
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document)  {
            return deserializeConstituency($document);
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
            '_id' => $object['constituency_id'],
            ...serializeConstituency($object),
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $object['constituency_id']],
                ['$set' => $document],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }
}
