<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class PartyPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => (int) $object['party_id'],
            'party_id' => (int) $object['party_id'],
            'name' => $object['name'] ?? null,
            'abbr_short' => $object['abbr_short'] ?? null,
            'abbr_long' => $object['abbr_long'] ?? null,
            'color' => $object['color'] ?? null,
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => (int) $document['_id'],
            'party_id' => (int) $document['party_id'],
            'name' => $document['name'] ?? null,
            'abbr_short' => $document['abbr_short'] ?? null,
            'abbr_long' => $document['abbr_long'] ?? null,
            'color' => $document['color'] ?? null,
        ];
    }
}
