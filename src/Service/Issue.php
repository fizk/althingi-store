<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use App\Presenter\{
    IssuePresenter,
    CategoryPresenter,
    SuperCategoryPresenter
};
use MongoDB\Model\BSONDocument;

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

        return (new IssuePresenter)->unserialize($document);
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
            return (new IssuePresenter)->unserialize($document);
        }, iterator_to_array($documents));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = (new IssuePresenter)->serialize($object);
        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $document['_id']],
                ['$set' => $document],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function addContentSuperCategory(int $assemblyId, int $issueId, string $category, mixed $object)
    {
        $id = [
            'assembly_id' => (int) $assemblyId,
            'issue_id' => (int) $issueId,
            'category' => $category
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $id],
                ['$addToSet' => [
                    'content_super_categories' => (new SuperCategoryPresenter)->serialize($object)
                ]],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function addContentCategory(int $assemblyId, int $issueId, string $category, mixed $object)
    {
        $id = [
            'assembly_id' => (int) $assemblyId,
            'issue_id' => (int) $issueId,
            'category' => $category
        ];

        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                ['_id' => $id],
                ['$addToSet' => [
                    'content_categories' => (new CategoryPresenter)->serialize($object)
                ]],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }
}
