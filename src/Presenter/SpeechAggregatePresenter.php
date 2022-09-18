<?php

namespace App\Presenter;

use ArrayIterator;
use MongoDB\Model\BSONDocument;

class SpeechAggregatePresenter implements Presenter
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
            'total' => (int) $document['total'],
            'congressman' => (new CongressmanPresenter)->unserialize($document['congressman'] ?? null),
            'parties' => array_map(
                fn (BSONDocument $party) => (new PartyPresenter)->unserialize($party),
                array_filter(
                    iterator_to_array($document['parties'] ?? new ArrayIterator()),
                    fn ($constituency) => $constituency !== null)
                ),
            'constituencies' => array_map(
                fn (BSONDocument $constituency) => (new ConstituencyPresenter)->unserialize($constituency),
                array_filter(
                    iterator_to_array($document['constituencies'] ?? new ArrayIterator()),
                    fn ($constituency) => $constituency !== null
                )
            ),
            'type' => array_values(array_filter($document['type']->getArrayCopy(), fn ($type) => !empty($type))),
            'time' => (int) $document['time'],
        ];
    }
}
