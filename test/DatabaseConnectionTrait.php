<?php

namespace App;

use MongoDB\Client;
use MongoDB\Database;

trait DatabaseConnectionTrait
{
    static private ?Client $connection = null;
    private ?Client $client = null;

    protected function setUp(): void
    {
        $this->client?->dropDatabase('althingi');
        parent::setUp();
    }


    protected function tearDown(): void
    {
        $this->client?->dropDatabase('althingi');
        parent::tearDown();
    }

    private function getDatabase(): Database
    {
        $dbHost = 'mongo-test';
        $dbUser = 'root';
        $dbPassword = 'example';

        self::$connection = $this->client = self::$connection ?: $client = new Client("mongodb://{$dbHost}", [
            'username' => $dbUser,
            'password' => $dbPassword,
        ], []);

        return $this->client->selectDatabase('althingi');
    }
}
