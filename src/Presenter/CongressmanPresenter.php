<?php

namespace App\Presenter;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use DateTime;

class CongressmanPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => (int) $object['congressman_id'],
            'congressman_id' => (int) $object['congressman_id'],
            'name' => $object['name'] ?? null,
            'birth' => $object['birth'] ?? null
                ? new UTCDateTime((new DateTime($object['birth']))->getTimestamp() * 1000)
                : null,
            'death' => null,
            'abbreviation' => $object['abbreviation'] ?? null,
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => (int) $document['_id'],
            'congressman_id' => (int) $document['congressman_id'],
            'name' => $document['name'] ?? null,
            'birth' => $document['birth'] ?? null
                ? $document['birth']->toDateTime()->format('c')
                : null,
            'death' => null,
            'abbreviation' => $document['abbreviation'] ?? null,
        ];
    }
}
