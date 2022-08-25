<?php

namespace App\Presenter;

use DateTime;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;

class InflationPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => (int) $object['id'],
            'id' => (int) $object['id'],
            'value' => (float) $object['value'],
            'date' => $object['date'] ?? null
                ? new UTCDateTime((new DateTime($object['date']))->getTimestamp() * 1000)
                : null,
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => (int) $document['_id'],
            'id' => (int) $document['id'],
            'value' => (float) $document['value'] ?? null,
            'date' => $document['date'] ?? null
                ? $document['date']->toDateTime()->format('c')
                : null,
        ];
    }
}
