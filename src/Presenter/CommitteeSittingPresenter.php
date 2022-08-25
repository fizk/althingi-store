<?php

namespace App\Presenter;

use DateTime;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;

class CommitteeSittingPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => (int) $object['committee_sitting_id'],
            'committee_sitting_id' => (int) $object['committee_sitting_id'],
            'order' => $object['order'] ?? null,
            'role' => $object['role'] ?? null,
            'from' => $object['from'] ?? null
                ? new UTCDateTime((new DateTime($object['from']))->getTimestamp() * 1000)
                : null,
            'to' => $object['to'] ?? null
                ? new UTCDateTime((new DateTime($object['to']))->getTimestamp() * 1000)
                : null,
            'assembly' => (new AssemblyPresenter)
                ->serialize($object['assembly'] ?? null),
            'committee' => (new CommitteePresenter)
                ->serialize($object['committee'] ?? null),
            'congressman' => (new CongressmanPresenter)
                ->serialize($object['congressman'] ?? null),
            'congressman_constituency' => (new ConstituencyPresenter)
                ->serialize($object['congressman_constituency'] ?? null),
            'congressman_party' => (new PartyPresenter)
                ->serialize($object['congressman_party'] ?? null),
            'first_committee_assembly' =>
                (new AssemblyPresenter)
                    ->serialize($object['first_committee_assembly'] ?? null),
            'last_committee_assembly' =>
                (new AssemblyPresenter)
                    ->serialize($object['last_committee_assembly'] ?? null),
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => (int) $document['_id'],
            'committee_sitting_id' => (int) $document['committee_sitting_id'],
            'order' => $document['order'] ?? null,
            'role' => $document['role'] ?? null,
            'from' => $document['from'] ?? null
                ? $document['from']->toDateTime()->format('c')
                : null,
            'to' => $document['to'] ?? null
                ? $document['to']->toDateTime()->format('c')
                : null,
            'assembly' => (new AssemblyPresenter)
                ->unserialize($document['assembly'] ?? null),
            'committee' => (new CommitteePresenter)
                ->unserialize($document['committee'] ? new BSONDocument([
                    ...$document['committee'] ?? [],
                    'first' => $document['first_committee_assembly'] ?? null,
                    'last' => $document['last_committee_assembly'] ?? null
                ]) : null),
            'congressman' => (new CongressmanPresenter)
                ->unserialize($document['congressman'] ?? null),
            'congressman_constituency' => (new ConstituencyPresenter)
                ->unserialize($document['congressman_constituency'] ?? null),
            'congressman_party' => (new PartyPresenter)
                ->unserialize($document['congressman_party'] ?? null),
            'first_committee_assembly' => (new AssemblyPresenter)
                ->unserialize($document['first_committee_assembly'] ?? null),
            'last_committee_assembly' => (new AssemblyPresenter)
                ->unserialize($document['last_committee_assembly'] ?? null),
        ];
    }
}
