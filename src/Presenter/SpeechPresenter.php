<?php

namespace App\Presenter;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use DateTime;

class SpeechPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return  [
            '_id' => [
                'assembly_id' => (int) $object['assembly']['assembly_id'],
                'issue_id' => (int) $object['issue']['issue_id'],
                'category' => (string)  $object['issue']['category'],
                'speech_id' => (string) $object['speech_id'],
            ],
            'speech_id' => $object['speech_id'],
            'plenary' => (new EmbeddedPlenaryPresenter)->serialize($object['plenary'] ?? null),
            'assembly' => (new AssemblyPresenter)->serialize($object['assembly'] ?? null),
            'issue' => (new EmbeddedIssuePresenter)->serialize($object['issue'] ?? null),
            'congressman' => (new CongressmanPresenter)->serialize($object['congressman'] ?? null),
            'congressman_party' => (new PartyPresenter)->serialize($object['congressman_party'] ?? null),
            'congressman_constituency' => (new ConstituencyPresenter)->serialize($object['congressman_constituency'] ?? null),
            'congressman_type' => $object['congressman_type'] ?? null,
            'from' => $object['from'] ?? null
                ? new UTCDateTime((new DateTime($object['from']))->getTimestamp() * 1000)
                : null,
            'to' => $object['to'] ?? null
                ? new UTCDateTime((new DateTime($object['to']))->getTimestamp() * 1000)
                : null,
            'text' => $object['text'] ?? null,
            'type' => $object['type'] ?? null,
            'iteration' => $object['iteration'] ?? null,
            'word_count' => $object['word_count'] ?? null,
            'validated' => $object['validated'] ?? false,
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return  [
            '_id' => $document['_id']->getArrayCopy(),
            'speech_id' => $document['speech_id'],
            'plenary' => (new EmbeddedPlenaryPresenter)->unserialize($document['plenary'] ?? null),
            'assembly' => (new AssemblyPresenter)->unserialize($document['assembly'] ?? null),
            'issue' => (new EmbeddedIssuePresenter)->unserialize($document['issue'] ?? null),
            'congressman' => (new CongressmanPresenter)->unserialize($document['congressman'] ?? null),
            'congressman_party' => (new PartyPresenter)->unserialize($document['congressman_party'] ?? null),
            'congressman_constituency' => (new ConstituencyPresenter)->unserialize($document['congressman_constituency'] ?? null),
            'congressman_type' => $document['congressman_type'],
            'from' => $document['from'] ?? null
                ? $document['from']->toDateTime()->format('c')
                : null,
            'to' => $document['to'] ?? null
                ? $document['to']->toDateTime()->format('c')
                : null,
            'text' => $document['text'] ?? null,
            'type' => $document['type'] ?? null,
            'iteration' => $document['iteration'] ?? null,
            'word_count' => $document['word_count'] ?? null,
            'validated' => $document['validated'] ?? false,
            'duration' => $document['duration'] ?? 0,
        ];
    }
}
