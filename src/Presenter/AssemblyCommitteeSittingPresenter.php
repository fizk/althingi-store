<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class AssemblyCommitteeSittingPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        return null;
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
            'assembly' => (new AssemblyPresenter)
                ->unserialize($document['assembly'] ?? null),
            'first_assembly' => (new AssemblyPresenter)
                ->unserialize($document['first_assembly'] ?? null),
            'last_assembly' => (new AssemblyPresenter)
                ->unserialize($document['last_assembly'] ?? null),
            'sessions' => array_map(function (BSONDocument $session) {
                return [
                    '_id' => (int) $session['_id'],
                    'congressman' => (new CongressmanPresenter)->unserialize($session['congressman'] ?? null),
                    'assembly' => (new AssemblyPresenter)->unserialize($session['assembly'] ?? null),
                    'sessions' => array_map(function (BSONDocument $document) {
                        return [
                            '_id' => (int) $document['_id'],
                            'abbr' => null,
                            'type' => $document['type'] ?? null,
                            'order' => $document['order'] ?? null,
                            'from' => $document['from'] ?? null
                                ? $document['from']->toDateTime()->format('c')
                                : null,
                            'to' => $document['to'] ?? null
                                ? $document['to']->toDateTime()->format('c')
                                : null,
                            'assembly' => (new AssemblyPresenter)
                                ->unserialize($document['assembly'] ?? null),
                            'congressman_party' => (new PartyPresenter)
                                ->unserialize($document['congressman_party'] ?? null),
                            'congressman_constituency' => (new ConstituencyPresenter)
                                ->unserialize($document['congressman_constituency'] ?? null)
                        ];
                    }, $session['sessions']?->getArrayCopy() ?? [])
                ];
            }, $document['sessions']?->getArrayCopy() ?? null)
        ];
    }
}
