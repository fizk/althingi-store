<?php

namespace App\Presenter;

use DateTime;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;

class EmbeddedPlenaryPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => [
                'assembly_id' => (int) $object['assembly_id'],
                'plenary_id' => (int) $object['plenary_id'],
            ],
            'plenary_id' => (int) $object['plenary_id'],
            'from' => $object['from'] ?? null
                ? new UTCDateTime((new DateTime($object['from']))->getTimestamp() * 1000)
                : null,
            'to' => $object['to'] ?? null
                ? new UTCDateTime((new DateTime($object['to']))->getTimestamp() * 1000)
                : null,
            'name' => $object['name'] ?? null,
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => $document['_id']->getArrayCopy(),
            'plenary_id' => (int) $document['plenary_id'],
            'duration' => $document['duration']?? null ? (int) $document['duration'] : 0,
            'from' => $document['from'] ?? null
                ? $document['from']->toDateTime()->format('c')
                : null,
            'to' => $document['to'] ?? null
                ? $document['to']->toDateTime()->format('c')
                : null,
            'name' => $document['name'] ?? null,
            'duration' => $document['duration'] ?? 0,
        ];
    }
}
