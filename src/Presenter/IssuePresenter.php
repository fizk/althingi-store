<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class IssuePresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => [
                'assembly_id' => (int) $object['assembly']['assembly_id'],
                'issue_id' => (int) $object['issue_id'],
                'category' => $object['category']
            ],
            'issue_id' => (int) $object['issue_id'],
            'category' => $object['category'],
            'name' => $object['name'] ?? null,
            'sub_name' => $object['sub_name'] ?? null,
            'type' => $object['type'] ?? null,
            'type_name' => $object['type_name'] ?? null,
            'type_subname' => $object['type_subname'] ?? null,
            'status' => $object['status'] ?? null,
            'question' => $object['question'] ?? null,
            'goal' => $object['goal'] ?? null,
            'major_changes' => $object['major_changes'] ?? null,
            'changes_in_law' => $object['changes_in_law'] ?? null,
            'costs_and_revenues' => $object['costs_and_revenues'] ?? null,
            'deliveries' => $object['deliveries'] ?? null,
            'additional_information' => $object['additional_information'] ?? null,
            'assembly' => (new AssemblyPresenter)->serialize($object['assembly'] ?? null),
            'congressman' => (new CongressmanPresenter)->serialize($object['congressman'] ?? null),
            'proponents' => array_map(function ($proponent) {
                return [
                    'congressman' => (new CongressmanPresenter)->serialize($proponent['congressman'] ?? null),
                    'party' => (new PartyPresenter)->serialize($proponent['party'] ?? null),
                    'constituency' =>  (new ConstituencyPresenter)->serialize($proponent['constituency'] ?? null),
                ];
            }, $object['proponents'] ?? []),
            'content_categories' => array_map(function ($category) {
                return (new CategoryPresenter)->serialize($category);
            }, $object['content_categories'] ?? []),
            'content_super_categories' => array_map(function ($category) {
                return (new SuperCategoryPresenter)->serialize($category);
            }, $object['content_super_categories'] ?? []),
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => $document['_id']->getArrayCopy(),
            'issue_id' => (int) $document['issue_id'],
            'category' => $document['category'],
            'name' => $document['name'] ?? null,
            'sub_name' => $document['sub_name'] ?? null,
            'type' => $document['type'] ?? null,
            'type_name' => $document['type_name'] ?? null,
            'type_subname' => $document['type_subname'] ?? null,
            'status' => $document['status'] ?? null,
            'question' => $document['question'] ?? null,
            'goal' => $document['goal'] ?? null,
            'major_changes' => $document['major_changes'] ?? null,
            'changes_in_law' => $document['changes_in_law'] ?? null,
            'costs_and_revenues' => $document['costs_and_revenues'] ?? null,
            'deliveries' => $document['deliveries'] ?? null,
            'additional_information' => $document['additional_information'] ?? null,
            'assembly' => (new AssemblyPresenter)->unserialize($document['assembly'] ?? null),
            'congressman' => (new CongressmanPresenter)->unserialize($document['congressman'] ?? null),
            'proponents' => array_map(function ($proponent) {
                return [
                    'congressman' => (new CongressmanPresenter)->unserialize($proponent['congressman'] ?? null),
                    'party' => (new PartyPresenter)->unserialize($proponent['party'] ?? null),
                    'constituency' => (new ConstituencyPresenter)->unserialize($proponent['constituency'] ?? null),
                ];
            }, $document['proponents']?->getArrayCopy() ?? []),
            'content_categories' => array_map(function ($category) {
                return (new CategoryPresenter)->unserialize($category);
            }, $document['content_categories']?->getArrayCopy() ?? []),
            'content_super_categories' => array_map(function ($category) {
                return (new SuperCategoryPresenter)->unserialize($category);
            }, $document['content_super_categories']?->getArrayCopy() ?? []),
        ];
    }
}
