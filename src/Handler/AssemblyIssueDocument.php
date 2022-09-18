<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceDocumentAware};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class AssemblyIssueDocument implements
    RequestHandlerInterface,
    ServiceDocumentAware
{
    use HandlerTrait;

    private Service\Document $documentService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $document = $this->documentService->get(
            $request->getAttribute('assembly_id'),
            $request->getAttribute('issue_id'),
            $request->getAttribute('document_id'),
        );
        return $document
            ? new JsonResponse($document, 200)
            : new EmptyResponse(404)
            ;
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true);
        $issue = [
            ...$body,
            'document_id' => $request->getAttribute('document_id'),
            'issue' => [
                ...$body['issue'],
                'issue_id' => $request->getAttribute('issue_id'),
                'category' => $request->getAttribute('category')
            ],
            'assembly' => [
                ...$body['assembly'],
                'assembly_id' => $request->getAttribute('assembly_id')
            ]
        ];

        $result = $this->documentService->store($issue);


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
