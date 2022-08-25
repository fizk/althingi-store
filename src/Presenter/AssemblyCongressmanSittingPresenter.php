<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class AssemblyCongressmanSittingPresenter implements Presenter
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
            'congressman' => (new CongressmanPresenter)->unserialize($document['congressman'] ?? null),
            'assembly' => (new AssemblyPresenter)->unserialize($document['assembly'] ?? null),
            'sessions' => array_map(function (BSONDocument $session) {
                return [
                    '_id' => (int) $session['_id'],
                    'session_id' => (int) $session['session_id'],
                    'abbr' => $session['abbr'] ?? null,
                    'type' => $session['type'] ?? null,
                    'from' => $session['from' ?? null]
                        ? $session['from']->toDateTime()->format('c')
                        : null,
                    'to' => $session['to']
                        ? $session['to']->toDateTime()->format('c')
                        : null,
                    'assembly' => (new AssemblyPresenter)
                        ->unserialize($session['assembly'] ?? null),
                    'congressman' => (new CongressmanPresenter)
                        ->unserialize($session['congressman'] ?? null),
                    'congressman_constituency' => (new ConstituencyPresenter)
                        ->unserialize($session['congressman_constituency'] ?? null),
                    'congressman_party' => (new PartyPresenter)
                        ->unserialize($session['congressman_party'] ?? null),
                ];
            }, $document['sessions']?->getArrayCopy() ?? null)
        ];
    }
}
