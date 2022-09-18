<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceDocumentAware};
use Laminas\Diactoros\Response\{JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class AssemblyIssueDocuments implements
    RequestHandlerInterface,
    ServiceDocumentAware
{
    use HandlerTrait;

    private Service\Document $documentService;
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $document = $this->documentService->fetchByIssue(
            $request->getAttribute('assembly_id'),
            $request->getAttribute('issue_id')
        );
        return new JsonResponse($document, 200);
    }

    public function setDocumentService(Service\Document $document): self
    {
        $this->documentService = $document;
        return $this;
    }
}
