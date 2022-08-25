<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class AssemblyMinisterSittingPresenter implements Presenter
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
            'ministry_id' => (int) $document['ministry_id'],
            'name' => $document['name'] ?? null,
            'abbr_short' => $document['abbr_short'] ?? null,
            'abbr_long' => $document['abbr_long'] ?? null,
            'first_ministry_assembly' => (new AssemblyPresenter)
                ->unserialize($document['first_ministry_assembly'] ?? null),
            'last_ministry_assembly' => (new AssemblyPresenter)
                ->unserialize($document['last_ministry_assembly'] ?? null),
            'congressmen' => array_map(function (BSONDocument $congressman) {
                return [
                    '_id' => (int) $congressman['_id'],
                    'minister_sitting_id' => (int) $congressman['minister_sitting_id'],
                    'from' => $congressman['from'] ?? null
                        ? $congressman['from']->toDateTime()->format('c')
                        : null,
                    'to' => $congressman['to'] ?? null
                        ? $congressman['to']->toDateTime()->format('c')
                        : null,
                    'assembly' => (new AssemblyPresenter)
                        ->unserialize($congressman['assembly'] ?? null),
                    'congressman' => (new CongressmanPresenter)
                        ->unserialize($congressman['congressman'] ?? null),
                    'congressman_constituency' => (new ConstituencyPresenter)
                        ->unserialize($congressman['congressman_constituency'] ?? null),
                    'congressman_party' => (new PartyPresenter)
                        ->unserialize($congressman['congressman_party'] ?? null),
                    'ministry' => (new MinistryPresenter)
                        ->unserialize($congressman['ministry'] ?? null),
                ];
            }, $document['congressmen']?->getArrayCopy() ?? [])
        ];
    }
}
