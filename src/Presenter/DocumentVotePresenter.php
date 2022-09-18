<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class DocumentVotePresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return  [
            '_id' => (int) $object['vote_item_id'],
            'vote_item_id' => (int) $object['vote_item_id'],
            'vote_id' => (int) $object['vote_id'],
            'congressman' => (new CongressmanPresenter)->serialize($object['congressman'] ?? null),
            'party' =>  (new PartyPresenter)->serialize($object['party'] ?? null),
            'constituency' => (new ConstituencyPresenter)->serialize($object['constituency'] ?? null),
            'vote' => $object['vote'] ?? null,
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => (int) $document['_id'],
            'vote_item_id' => (int) $document['vote_item_id'],
            'vote_id' => (int) $document['vote_id'],
            'congressman' => (new CongressmanPresenter)->serialize($document['congressman'] ?? null),
            'party' => (new PartyPresenter)->serialize($document['party'] ?? null),
            'constituency' => (new ConstituencyPresenter)->serialize($document['constituency'] ?? null),
            'vote' => $document['vote'] ?? null,
        ];
    }
}
