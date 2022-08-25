<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class MinistryPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => (int) $object['ministry_id'],
            'ministry_id' => (int) $object['ministry_id'],
            'name' => $object['name'] ?? null,
            'abbr_short' => $object['abbr_short'] ?? null,
            'abbr_long' => $object['abbr_long'] ?? null,
            'first' => is_numeric($object['first'] ?? null)
                ? null
                : (new AssemblyPresenter)->serialize($object['first'] ?? null),
            'last' => is_numeric($object['last'] ?? null)
                ? null
                : (new AssemblyPresenter)->serialize($object['last'] ?? null),
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => (int) $document['_id'],
            'ministry_id' => (int) $document['ministry_id'],
            'name' => $document['name'] ?? null,
            'abbr_short' => $document['abbr_short'] ?? null,
            'abbr_long' => $document['abbr_long'] ?? null,
            'first' => (new AssemblyPresenter)->unserialize($document['first'] ?? null),
            'last' => (new AssemblyPresenter)->unserialize($document['last'] ?? null),
        ];
    }
}
