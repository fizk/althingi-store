<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class ConstituencyPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => (int) $object['constituency_id'],
            'constituency_id' => (int) $object['constituency_id'],
            'name' => $object['name'] ?? null,
            'abbr_short' => $object['abbr_short'] ?? null,
            'abbr_long' => $object['abbr_long'] ?? null,
            'description' => $object['description'] ?? null,
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => (int) $document['_id'],
            'constituency_id' => (int) $document['constituency_id'],
            'name' => $document['name'] ?? null,
            'abbr_short' => $document['abbr_short'] ?? null,
            'abbr_long' => $document['abbr_long'] ?? null,
            'description' => $document['description'] ?? null,
        ];
    }
}
