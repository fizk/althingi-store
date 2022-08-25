<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class AssemblyPresidentSittingPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        return null;
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => $document['_id'],
            'congressman' => (new CongressmanPresenter)->unserialize($document['congressman'] ?? null),
            'assembly' => (new AssemblyPresenter)->unserialize($document['assembly'] ?? null),
            'sessions' => array_map(function (BSONDocument $session) {
                return [
                    '_id' => $session['_id'],
                    'abbr' => $session['abbr'] ?? null,
                    'congressman_party' => (new PartyPresenter)->unserialize($session['congressman_party'] ?? null),
                    'congressman_constituency' => (new ConstituencyPresenter)->unserialize($session['congressman_constituency'] ?? null),
                    'from' => $session['from'] ?? null
                        ? $session['from']->toDateTime()->format('c')
                        : null,
                    'to' => $session['to'] ?? null
                        ? $session['to']->toDateTime()->format('c')
                        : null,
                    'type' => $session['type'] ?? null,
                ];
            }, $document['sessions']?->getArrayCopy() ?? [])
        ];
    }
}
