<?php

namespace App\Presenter;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use ArrayIterator;
use DateTime;

class DocumentPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return  [
            '_id' => [
                'assembly_id' => (int) $object['assembly']['assembly_id'],
                'issue_id' => (int) $object['issue']['issue_id'],
                'category' => $object['issue']['category'],
                'document_id' => (int) $object['document_id'],
            ],
            'document_id' => (int) $object['document_id'],
            'issue' => (new EmbeddedIssuePresenter)->serialize($object['issue']),
            'assembly' => (new AssemblyPresenter)->serialize($object['assembly']),
            'date' => $object['date'] ?? null
                ? new UTCDateTime((new DateTime($object['date']))->getTimestamp() * 1000)
                : null,
            'url' => $object['url'] ?? null,
            'type' => $object['type'] ?? null,
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return  [
            '_id' => $document['_id']->getArrayCopy(),
            'document_id' => (int) $document['document_id'],
            'issue' => (new EmbeddedIssuePresenter)->unserialize($document['issue']),
            'assembly' => (new AssemblyPresenter)->unserialize($document['assembly']),
            'date' => $document['date'] ?? null
                ? $document['date']->toDateTime()->format('c')
                : null,
            'url' => $document['url'] ?? null,
            'type' => $document['type'] ?? null,
            'proponents' => [],
            'votes' => array_map(function (BSONDocument $vote) use ($document) {
                return (new DocumentOutcomePresenter)->unserialize(new BSONDocument([
                    ...$vote,
                    'issue' => (new EmbeddedIssuePresenter)->unserialize($document['issue']),
                    'assembly' => (new AssemblyPresenter)->unserialize($document['assembly']),
                ]));
            }, iterator_to_array($document['votes'] ?? new ArrayIterator()))
        ];
    }
}
