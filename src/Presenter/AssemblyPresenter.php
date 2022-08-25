<?php

namespace App\Presenter;

use DateTime;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;

class AssemblyPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return  [
            '_id' => (int) $object['assembly_id'],
            'assembly_id' => (int) $object['assembly_id'],
            'from' => $object['from'] ?? null
                ? new UTCDateTime((new DateTime($object['from']))->getTimestamp() * 1000)
                : null,
            'to' => $object['to'] ?? null
                ? new UTCDateTime((new DateTime($object['to']))->getTimestamp() * 1000)
                : null,
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => (int) $document['_id'],
            'assembly_id' => (int) $document['assembly_id'],
            'from' => $document['from'] ?? null
                ? $document['from']->toDateTime()->format('c')
                : null,
            'to' => $document['to'] ?? null
                ? $document['to']->toDateTime()->format('c')
                : null,
        ];
    }
}
