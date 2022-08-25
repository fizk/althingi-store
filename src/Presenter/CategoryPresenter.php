<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class CategoryPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => (int) $object['category_id'],
            'category_id' => (int) $object['category_id'],
            'super_category_id' => (int) $object['super_category_id'],
            'title' => $object['title'] ?? null,
            'description' => $object['description'] ?? null,
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => (int) $document['_id'],
            'category_id' => (int) $document['category_id'],
            'super_category_id' => (int) $document['super_category_id'],
            'title' => $document['title'] ?? null,
            'description' => $document['description'] ?? null,
        ];
    }
}
