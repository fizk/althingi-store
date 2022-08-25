<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

class SuperCategoryPresenter implements Presenter
{
    public function serialize(?array $object): ?array
    {
        if (!$object) return null;
        return [
            '_id' => (int) $object['super_category_id'],
            'super_category_id' => (int) $object['super_category_id'],
            'title' => $object['title'] ?? null,
        ];
    }

    public function unserialize(?BSONDocument $document): ?array
    {
        if (!$document) return null;
        return [
            '_id' => (int) $document['_id'],
            'super_category_id' => (int) $document['super_category_id'],
            'title' => $document['title'] ?? null,
        ];
    }
}
