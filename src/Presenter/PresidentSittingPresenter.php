<?php

namespace App\Presenter;

use DateTime;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;

class PresidentSittingPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => (int) $object['president_id'],
            'president_id' => (int) $object['president_id'],
            'from' => $object['from'] ?? null
                ? new UTCDateTime((new DateTime($object['from']))->getTimestamp() * 1000)
                : null,
            'to' => $object['to'] ?? null
                ? new UTCDateTime((new DateTime($object['to']))->getTimestamp() * 1000)
                : null,
            'title' => $object['title'] ?? null,
            'abbr' => $object['abbr'] ?? null,
            'assembly' => (new AssemblyPresenter)->serialize($object['assembly'] ?? null),
            'congressman' => (new CongressmanPresenter)->serialize($object['congressman'] ?? null),
            'congressman_party' => (new PartyPresenter)->serialize($object['congressman_party'] ?? null),
            'congressman_constituency' => (new ConstituencyPresenter)->serialize($object['congressman_constituency'] ?? null),
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => (int) $document['_id'],
            'president_id' => (int) $document['president_id'],
            'from' => $document['from'] ?? null
                ? $document['from']->toDateTime()->format('c')
                : null,
            'to' => $document['to'] ?? null
                ? $document['to']->toDateTime()->format('c')
                : null,
            'title' => $document['title'] ?? null,
            'abbr' => $document['abbr'] ?? null,
            'assembly' => (new AssemblyPresenter)->unserialize($document['assembly'] ?? null),
            'congressman' => (new CongressmanPresenter)->unserialize($document['congressman'] ?? null),
            'congressman_party' => (new PartyPresenter)->unserialize($document['congressman_party'] ?? null),
            'congressman_constituency' => (new ConstituencyPresenter)->unserialize($document['congressman_constituency'] ?? null),
        ];
    }
}
