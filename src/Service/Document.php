<?php

namespace App\Service;

use App\Service\SourceDatabaseTrait;
use App\Decorator\SourceDatabaseAware;
use App\Presenter\{
    DocumentOutcomePresenter,
    DocumentPresenter
};
use MongoDB\Model\BSONDocument;

class Document implements SourceDatabaseAware
{
    const COLLECTION = 'document';
    use SourceDatabaseTrait;

    public function get(int $assemblyId, int $issueId, int $documentId): ?array
    {
        $document = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->findOne(['_id' => [
                'assembly_id' => $assemblyId,
                'issue_id' => $issueId,
                'category' => 'a',
                'document_id' => $documentId,
            ]]);

        return (new DocumentPresenter())->unserialize($document);
    }

    public function fetchByIssue(int $assemblyId, int $issueId,)
    {
        $documents = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->find([
                '_id.assembly_id' => $assemblyId,
                '_id.issue_id' => $issueId,
                '_id.category' => 'a',
            ]);

        return array_map(function (BSONDocument $document) {
            return (new DocumentPresenter())->unserialize($document);
        }, iterator_to_array($documents));
    }

    /**
     * @return int | not modified = 0, create = 1, update = 2
     */
    public function store(mixed $object): int
    {
        $document = (new DocumentPresenter())->serialize($object);

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
    public function addVoteResult(int $assemblyId, int $issueId, int $documentId, mixed $object): int
    {
        $result = $this->getSourceDatabase()
            ->selectCollection(self::COLLECTION)
            ->updateOne(
                [
                    '_id.assembly_id' => (int) $assemblyId,
                    '_id.issue_id' => (int) $issueId,
                    '_id.category' => 'a',
                    '_id.document_id' => (int) $documentId,
                ],
                ['$addToSet' => [
                    'votes' => (new DocumentOutcomePresenter)->serialize($object ?? null),
                ]],
                ['upsert' => true]
            );

        return ($result->getModifiedCount() << 1) + $result->getUpsertedCount();
    }

    // public function updateAssembly(?array $assembly): void
    // {
    //     if (!$assembly) {
    //         return;
    //     }

    //     $this->getSourceDatabase()
    //         ->selectCollection(self::COLLECTION)
    //         ->updateMany(
    //             ['assembly.assembly_id' => $assembly['assembly_id']],
    //             ['$set' => ['assembly' => (new AssemblyPresenter)->serialize($assembly)]],
    //             ['upsert' => false]
    //         );
    // }

    // public function updateIssue(?array $issue): void
    // {
    //     if (!$issue) {
    //         return;
    //     }

    //     $this->getSourceDatabase()
    //         ->selectCollection(self::COLLECTION)
    //         ->updateMany(
    //             ['assembly.assembly_id' => $issue['assembly_id']],
    //             ['$set' => ['assembly' => (new EmbeddedIssuePresenter)->serialize($assembly)]],
    //             ['upsert' => false]
    //         );
    // }
}
