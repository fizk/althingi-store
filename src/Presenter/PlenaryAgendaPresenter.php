<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class PlenaryAgendaPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        $stuff =  [
            '_id' => [
                'assembly_id' => (int) $object['assembly']['assembly_id'],
                'plenary_id' => (int) $object['plenary']['plenary_id'],
                'item_id' => (int) $object['item_id']
            ],
            'item_id' => (int) $object['item_id'] ?? null,
            'plenary' => $object['plenary'] ?? null
                ? (new EmbeddedPlenaryPresenter)->serialize([
                    ...$object['plenary'],
                    'assembly_id' => (int) $object['assembly']['assembly_id']
                ])
                : null,
            'issue' => $object['issue'] ?? null
                ? (new EmbeddedIssuePresenter)->serialize([
                    ...$object['issue'],
                    'assembly_id' => (int) $object['assembly']['assembly_id']
                ])
                : null,
            'assembly' => (new AssemblyPresenter)->serialize($object['assembly'] ?? null),
            'iteration_type' => $object['iteration_type'] ?? null,
            'iteration_continue' => $object['iteration_continue'] ?? null,
            'iteration_comment' => $object['iteration_comment'] ?? null,
            'comment' => $object['comment'] ?? null,
            'comment_type' => $object['comment_type'] ?? null,
            'posed' => (new CongressmanPresenter)->serialize($object['posed'] ?? null),
            'posed_party' => (new PartyPresenter)->serialize($object['posed_party'] ?? null),
            'posed_constituency' => (new ConstituencyPresenter)->serialize($object['posed_constituency'] ?? null),
            'posed_title' => $object['posed_title'] ?? null,
            'answerer' => (new CongressmanPresenter)->serialize($object['answerer'] ?? null),
            'answerer_party' => (new PartyPresenter)->serialize($object['answerer_party'] ?? null),
            'answerer_constituency' => (new ConstituencyPresenter)->serialize($object['answerer_constituency'] ?? null),
            'answerer_title' => $object['answerer_title'] ?? null,
            'counter_answerer' => (new CongressmanPresenter)->serialize($object['counter_answerer'] ?? null),
            'counter_answerer_party' => (new PartyPresenter)->serialize($object['counter_answerer_party'] ?? null),
            'counter_answerer_constituency' => (new ConstituencyPresenter)->serialize($object['counter_answerer_constituency'] ?? null),
            'counter_answerer_title' => $object['counter_answerer_title'] ?? null,
            'instigator' => (new CongressmanPresenter)->serialize($object['instigator'] ?? null),
            'instigator_party' => (new PartyPresenter)->serialize($object['instigator_party'] ?? null),
            'instigator_constituency' => (new ConstituencyPresenter)->serialize($object['instigator_constituency'] ?? null),
            'instigator_title' => $object['instigator_title'] ?? null,
        ];

        return $stuff;
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => $document['_id']->getArrayCopy(),
            'item_id' => (int) $document['item_id'] ?? null,
            'plenary' => (new PlenaryPresenter)->unserialize($document['plenary'] ?? null),
            'issue' => (new EmbeddedIssuePresenter)->unserialize($document['issue'] ?? null),
            'assembly' => (new AssemblyPresenter)->unserialize($document['assembly'] ?? null),
            'iteration_type' => $document['iteration_type'] ?? null,
            'iteration_continue' => $document['iteration_continue'] ?? null,
            'iteration_comment' => $document['iteration_comment'] ?? null,
            'comment' => $document['comment'] ?? null,
            'comment_type' => $document['comment_type'] ?? null,
            'posed' => (new CongressmanPresenter)->unserialize($document['posed'] ?? null),
            'posed_party' => (new PartyPresenter)->unserialize($document['posed_party'] ?? null),
            'posed_constituency' => (new ConstituencyPresenter)->unserialize($document['posed_constituency'] ?? null),
            'posed_title' => $document['posed_title'] ?? null,
            'answerer' => (new CongressmanPresenter)->unserialize($document['answerer'] ?? null),
            'answerer_party' => (new PartyPresenter)->unserialize($document['answerer_party'] ?? null),
            'answerer_constituency' => (new ConstituencyPresenter)->unserialize($document['answerer_constituency'] ?? null),
            'answerer_title' => $document['answerer_title'] ?? null,
            'counter_answerer' => (new CongressmanPresenter)->unserialize($document['counter_answerer'] ?? null),
            'counter_answerer_party' => (new PartyPresenter)->unserialize($document['counter_answerer_party'] ?? null),
            'counter_answerer_constituency' => (new ConstituencyPresenter)->unserialize($document['counter_answerer_constituency'] ?? null),
            'counter_answerer_title' => $document['counter_answerer_title'] ?? null,
            'instigator' => (new CongressmanPresenter)->unserialize($document['instigator'] ?? null),
            'instigator_party' => (new PartyPresenter)->unserialize($document['instigator_party'] ?? null),
            'instigator_constituency' => (new ConstituencyPresenter)->unserialize($document['instigator_constituency'] ?? null),
            'instigator_title' => $document['instigator_title'] ?? null,
        ];
    }
}
