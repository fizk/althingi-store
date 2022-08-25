<?php

namespace App\Presenter;

use MongoDB\Model\BSONDocument;

interface Presenter
{
    public function serialize(?array $object): ?array;

    public function unserialize(?BSONDocument $document): ?array;
}
