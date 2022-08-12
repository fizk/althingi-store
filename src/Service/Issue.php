<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use MongoDB\Model\BSONDocument;
use function App\{deserializeIssue, serializeIssue};

class Issue implements SourceDatabaseAware
{
    const COLLECTION = 'issue';
    use SourceDatabaseTrait;

    public function get(int $assemblyId, int $issueId, string $category): ?array
    {
        $id = [
            'assembly_id' => (int) $assemblyId,
            'issue_id' => $issueId,
            'category' => $category
        ];

        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => $id]);

        return $document? deserializeIssue($document) : null;
    }

    /**
     * @todo filter by $params
     */
    public function fetchByAssembly(int $assemblyId, ?string $category = null, array $params = []): array
    {
        $query = array_merge(
            ['_id.assembly_id' => $assemblyId],
            $category ? ['_id.category' => $category] : []
        );

        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->find($query);

        return array_map(function (BSONDocument $document) {
            return deserializeIssue($document);
        }, iterator_to_array($documents));
    }

    public function store(mixed $object): int
    {
        $id = [
            'assembly_id' => (int) $object['assembly']['assembly_id'],
            'issue_id' => (int) $object['issue_id'],
            'category' => $object['category']
        ];

        $document = serializeIssue([
            '_id' => $id,
            ...$object,
        ]);

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $id],
                ['$set' => $document],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }
}
