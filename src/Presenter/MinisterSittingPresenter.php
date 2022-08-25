<?php

namespace App\Presenter;

use DateTime;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;

class MinisterSittingPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => (int) $object['minister_sitting_id'],
            'minister_sitting_id' => (int) $object['minister_sitting_id'],
            'from' => $object['from'] ?? null
                ? new UTCDateTime((new DateTime($object['from']))->getTimestamp() * 1000)
                : null,
            'to' => $object['to'] ?? null
                ? new UTCDateTime((new DateTime($object['to']))->getTimestamp() * 1000)
                : null,
            'assembly' => (new AssemblyPresenter)
                ->serialize($object['assembly'] ?? null),
            'ministry' => (new MinistryPresenter)
                ->serialize($object['ministry'] ?? null),
            'congressman' => (new CongressmanPresenter)
                ->serialize($object['congressman'] ?? null),
            'congressman_constituency' => (new ConstituencyPresenter)
                ->serialize($object['congressman_constituency'] ?? null),
            'congressman_party' => (new PartyPresenter)
                ->serialize($object['congressman_party'] ?? null),
            'first_ministry_assembly' => (new AssemblyPresenter)
                ->serialize($object['first_ministry_assembly'] ?? null),
            'last_ministry_assembly' => (new AssemblyPresenter)
                ->serialize($object['last_ministry_assembly'] ?? null),

        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => (int) $document['_id'],
            'minister_sitting_id' => (int) $document['minister_sitting_id'],
            'from' => $document['from'] ?? null
                ? $document['from']->toDateTime()->format('c')
                : null,
            'to' => $document['to'] ?? null
                ? $document['to']->toDateTime()->format('c')
                : null,
            'assembly' => (new AssemblyPresenter)->unserialize($document['assembly'] ?? null),
            'ministry' => $document['ministry'] ?? null
                ? (new MinistryPresenter)->unserialize(new BSONDocument([
                    ...$document['ministry'],
                    'first' => $document['first_ministry_assembly'] ?? null,
                    'last' => $document['last_ministry_assembly'] ?? null,
                ]) ?? null)
                : null,
            'congressman' => (new CongressmanPresenter)
                ->unserialize($document['congressman'] ?? null),
            'congressman_constituency' => (new ConstituencyPresenter)
                ->unserialize($document['congressman_constituency'] ?? null),
            'congressman_party' => (new PartyPresenter)
                ->unserialize($document['congressman_party'] ?? null),
            'first_ministry_assembly' => (new AssemblyPresenter)
                ->unserialize($document['first_ministry_assembly'] ?? null),
            'last_ministry_assembly' => (new AssemblyPresenter)
                ->unserialize($document['last_ministry_assembly'] ?? null),
        ];
    }
}
