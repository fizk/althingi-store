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
    private int $maxIssueCount = 25;
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

    public function fetchByAssembly(int $assemblyId, ?string $category = null, $pointer = null, array $params = []): array
    {
        $documents = [];
        $nextId = null;
        $terminal = true;
        $active = false;
        $counter = 0;

        if ($pointer) {
            $pointer = explode('-', $pointer);
            $pointer[0] = (int) $pointer[0];
            $pointer[1] = (string) $pointer[1];
        }

        $query = array_merge(
            ['_id.assembly_id' => $assemblyId],
            $category ? ['_id.category' => $category] : [],
            count($params) ? ['type' => ['$in' => $params]] : []
        );

        $cursor = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->find($query);

        $cursor->rewind();

        while ($cursor->valid()) {
            /** @var \MongoDB\Model\BSONDocument */
            $current = $cursor->current();

            if ($pointer === null) {
                $active = true;
            }
            if ($current['issue_id'] === ($pointer[0] ?? null) && $current['category'] === ($pointer[1] ?? null)) {
                $active = true;
                $counter += 1;
                $documents[] = $current;

                $cursor->next();
                if ($cursor->valid()) {
                    $next = $cursor->current();
                    $nextId = "{$next['issue_id']}-{$next['category']}";
                    continue;
                } else {
                    $nextId = null;
                    $terminal = true;
                    break;
                }
            }
            if ($active && $counter < $this->maxIssueCount) {
                $counter += 1;
                $documents[] = $current;

                $cursor->next();
                if ($cursor->valid()) {
                    $next = $cursor->current();
                    $nextId = "{$next['issue_id']}-{$next['category']}";
                    continue;
                } else {
                    $nextId = null;
                    $terminal = true;
                    break;
                }
            }

            $cursor->next();
            $terminal = false;
        }

        return [
            'list' => array_map(fn (BSONDocument $document) =>
                (new IssuePresenter)->unserialize($document), $documents
            ),
            'next' => $nextId,
            'terminal' => $terminal,
        ];
    }

    public function fetchContentCategoriesAggregation(int $assemblyId, array $params = [])
    {

        $id = array_merge([
            '_id.assembly_id' => $assemblyId,
            '_id.category' => 'a'
        ], count($params) ? ['type' => ['$in' => $params]] : []);

        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => $id
                ],
                [
                    '$unwind' => '$content_super_categories'
                ],
                [
                    '$group' => [
                        '_id' => '$content_super_categories._id',
                        'total' => ['$count' => (object)[]],
                        'aggregation' => ['$first'=> '$content_super_categories.title'],
                        'content' => ['$addToSet' => '$content_super_categories'],
                    ]
                ]
            ]
        );

        return array_map(function (BSONDocument $document) {
            return [
                ...$document,
                'content' => array_map(function (BSONDocument $category) {
                    return (new SuperCategoryPresenter)->unserialize($category);
                }, iterator_to_array($document['content']))
            ];
        }, iterator_to_array($documents));
    }

    public function fetchStatusAggregation(int $assemblyId, array $params = [])
    {
        $id = array_merge([
            '_id.assembly_id' => $assemblyId,
            '_id.category' => 'a'
        ], count($params) ? ['type' => ['$in' => $params]] : []);

        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->aggregate([
                [
                    '$match' => $id
                ],
                [
                    '$group' => [
                        '_id' => '$status',
                        'total' => ['$count' => (object)[]],
                        'aggregation' => ['$first' => '$status'],
                        'content' => ['$addToSet' => '$$ROOT']
                    ]
                ],
                [
                '$match' => [
                    '_id' => ['$ne' => null]
                ]
            ]
        ]);

        return array_map(function (BSONDocument $document) {
            return [
                ...$document,
                'content' => [],

                //  @todo Returning all issues, might be bit too much :)

                // 'content' => array_map(function (BSONDocument $issue) {
                //     return (new IssuePresenter)->unserialize($issue);
                // }, $document['issues']?->getArrayCopy())
            ];
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
