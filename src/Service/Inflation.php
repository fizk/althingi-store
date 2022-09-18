<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use App\Presenter\InflationPresenter;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use DateTime;

class Inflation implements SourceDatabaseAware
{
    const COLLECTION = 'inflation';
    use SourceDatabaseTrait;

    public function get(int $id): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return (new InflationPresenter)->unserialize($document);
    }

    public function fetchRange(DateTime $from, DateTime $to): array
    {
        $from = new UTCDateTime($from->getTimestamp() * 1000);
        $to = new UTCDateTime($to->getTimestamp() * 1000);
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->find([
                '$and' => [
                    ['date' => ['$gte' => $from]],
                    ['date' => ['$lte' => $to]],
                ]
            ]);

        return array_map(function ($document) {
            return (new InflationPresenter)->unserialize($document);
        }, iterator_to_array($documents, false));
    }

    public function fetch(): array
    {
        return array_map(function (BSONDocument $document)  {
            return (new InflationPresenter)->unserialize($document);
        }, iterator_to_array(
            $this->getSourceDatabase()->selectCollection(self::COLLECTION)->find()
        ));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = (new InflationPresenter)->serialize($object);

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $document['id']],
                ['$set' => $document],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }
}
