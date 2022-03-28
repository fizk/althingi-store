<?php

namespace App\Service;

use MongoDB\Database;

trait SourceDatabaseTrait
{
    private Database $database;

    public function getSourceDatabase(): Database
    {
        return $this->database;
    }

    public function setSourceDatabase(Database $database): self
    {
        $this->database = $database;
        return $this;
    }
}
