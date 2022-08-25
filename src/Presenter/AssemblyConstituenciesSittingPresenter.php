<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class AssemblyConstituenciesSittingPresenter implements Presenter
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
            "constituency_id" => (int) $document['constituency_id'],
            "name" => $document['name'] ?? null,
            "abbr_short" => $document['abbr_short'] ?? null,
            "abbr_long" => $document['abbr_long'] ?? null,
            "description" => $document['description'] ?? null,
            'assembly' => (new AssemblyPresenter)->unserialize($document['assembly'] ?? null),
            'congressmen' => array_map(function (BSONDocument $session) {
                return [
                    '_id' => (int) "{$session['_id']['congressman']}{$session['_id']['constituency']}",
                    'congressman' => (new CongressmanPresenter)
                        ->unserialize($session['congressman'] ?? null),
                    'assembly' => (new AssemblyPresenter)
                        ->unserialize($session['assembly'] ?? null),
                    'sessions' => array_map(function(BSONDocument $record) {
                        return [
                            '_id' => (int) $record['_id'],
                            'congressman_party' => (new PartyPresenter)
                                ->unserialize($record['congressman_party'] ?? null),
                            'congressman_constituency' => (new ConstituencyPresenter)
                                ->unserialize($record['congressman_constituency'] ?? null),
                            'type' => $record['type'] ?? null,
                            'from' => $record['from'] ?? null
                                ? $record['from']->toDateTime()->format('c')
                                : null,
                            'to' => $record['to'] ?? null
                                ? $record['to']->toDateTime()->format('c')
                                : null,
                        ];
                    }, $session['sessions']?->getArrayCopy() ?? null),
                ];
            }, $document['congressmen']?->getArrayCopy() ?? null)
        ];
    }
}
