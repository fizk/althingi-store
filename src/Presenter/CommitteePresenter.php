<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class CommitteePresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => (int) $object['committee_id'],
            'committee_id' => (int) $object['committee_id'],
            'name' => $object['name'] ?? null,
            'abbr_long' => $object['abbr_long'] ?? null,
            'abbr_short' => $object['abbr_short'] ?? null,
            'first' => (new AssemblyPresenter)->serialize($object['first'] ?? null),
            'last' => (new AssemblyPresenter)->serialize($object['last'] ?? null),
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;

        return [
            '_id' => (int) $document['_id'],
            'committee_id' => (int) $document['committee_id'],
            'name' => $document['name'] ?? null,
            'abbr_long' => $document['abbr_long'] ?? null,
            'abbr_short' => $document['abbr_short'] ?? null,
            'first' => (new AssemblyPresenter)->unserialize($document['first'] ?? null),
            'last' => (new AssemblyPresenter)->unserialize($document['last'] ?? null),
        ];
    }
}
