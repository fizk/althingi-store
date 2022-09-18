<?php

namespace App\Presenter;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use DateTime;

class DocumentOutcomePresenter implements Presenter
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
                'vote_id' => (int) $object['vote_id'],
            ],
            'vote_id' => (int) $object['vote_id'] ?? null,
            'date' => $object['date'] ?? null
                ? new UTCDateTime((new DateTime($object['date']))->getTimestamp() * 1000)
                : null,
            'type' => $object['type'] ?? null,
            'outcome' => $object['outcome'] ?? null,
            'method' => $object['method'] ?? null,
            'yes' => (int) $object['yes'] ?? 0,
            'no' => (int) $object['no'] ?? 0,
            'inaction' => (int) $object['inaction'] ?? 0,
            'items' => [],
            'assembly' => (new AssemblyPresenter)->serialize($object['assembly'] ?? null),
            'issue' => (new EmbeddedIssuePresenter)->serialize($object['issue'] ?? null),
            'committee' => (new CommitteePresenter)->serialize($object['committee'] ?? null),
            'committee_first_assembly' => (new AssemblyPresenter)->serialize($object['committee_first_assembly'] ?? null),
            'committee_last_assembly' => (new AssemblyPresenter)->serialize($object['committee_last_assembly'] ?? null),
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return  [
            '_id' => $document['_id']->getArrayCopy(),
            'vote_id' => (int) $document['vote_id'] ?? null,
            'date' => $document['date'] ?? null
                ? $document['date']->toDateTime()->format('c')
                : null,
            'type' => $document['type'] ?? null,
            'outcome' => $document['outcome'] ?? null,
            'method' => $document['method'] ?? null,
            'yes' => (int) $document['yes'] ?? 0,
            'no' => (int) $document['no'] ?? 0,
            'inaction' => (int) $document['inaction'] ?? 0,
            'items' => [],
            'assembly' => (new AssemblyPresenter)->unserialize($document['assembly'] ?? null),
            'issue' => (new EmbeddedIssuePresenter)->unserialize($document['issue'] ?? null),
            'committee' => (new CommitteePresenter)->unserialize($document['committee'] ?? null),
            'committee_first_assembly' => (new AssemblyPresenter)->unserialize($document['committee_first_assembly'] ?? null),
            'committee_last_assembly' => (new AssemblyPresenter)->unserialize($document['committee_last_assembly'] ?? null),
        ];
    }
}
