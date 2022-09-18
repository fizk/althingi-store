<?php

namespace App\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Laminas\Diactoros\Response\{EmptyResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceDocumentAware};

class AssemblyIssueDocumentsOutcome implements
    RequestHandlerInterface,
    ServiceDocumentAware
{
    use HandlerTrait;

    private Service\Document $documentService;

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true);

        $result = $this->documentService->addVoteResult(
            $request->getAttribute('assembly_id'),
            $request->getAttribute('issue_id'),
            $request->getAttribute('document_id'),
            $body
        );

        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setDocumentService(Service\Document $document): self
    {
        $this->documentService = $document;
        return $this;
    }
}
