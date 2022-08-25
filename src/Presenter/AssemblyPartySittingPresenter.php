<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class AssemblyPartySittingPresenter implements Presenter
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
            'party_id' => (int) $document['party_id'],
            'name' => $document['name'] ?? null,
            'abbr_short' => $document['abbr_short'] ?? null,
            'abbr_long' => $document['abbr_long'] ?? null,
            'color' => $document['color'] ?? null,
            'assembly' => (new AssemblyPresenter)->unserialize($document['assembly'] ?? null),
            'congressmen' => array_map(function (BSONDocument $session) {
                return [
                    '_id' => (int)"{$session['_id']['congressman']}{$session['_id']['party']}",
                    'congressman' => (new CongressmanPresenter)->unserialize($session['congressman'] ?? null),
                    'assembly' => (new AssemblyPresenter)->unserialize($session['assembly'] ?? null),
                    'sessions' => array_map(function (BSONDocument $record) {
                        return [
                            '_id' => $record['_id'],
                            'congressman_party' => (new PartyPresenter)
                                ->unserialize($record['congressman_party'] ?? null),
                            'congressman_constituency' => (new ConstituencyPresenter)
                                ->unserialize($record['congressman_constituency'] ?? null),
                            'from' => $record['from'] ?? null
                                ? $record['from']->toDateTime()->format('c')
                                : null,
                            'to' => $record['to'] ?? null
                                ? $record['to']->toDateTime()->format('c')
                                : null,
                            'type' => $record['type'] ?? null
                        ];
                    }, $session['sessions']?->getArrayCopy() ?? null)
                ];
            }, $document['congressmen']?->getArrayCopy() ?? null)
        ];
    }
}
