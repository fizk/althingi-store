<?php

namespace App\Decorator;

use MongoDB\Database;

interface SourceDatabaseAware
{
    public function getSourceDatabase(): Database;

    public function setSourceDatabase(Database $database): self;
}
