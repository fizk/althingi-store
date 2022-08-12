<?php

namespace App\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceIssueAware, ServicePlenaryAgendaAware};

class AssemblyIssue implements
    RequestHandlerInterface,
    ServiceIssueAware,
    ServicePlenaryAgendaAware
{
    use HandlerTrait;

    private Service\Issue $issueService;
    private Service\PlenaryAgenda $plenaryAgendaService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $document = $this->issueService->get(
            $request->getAttribute('assembly_id'),
            $request->getAttribute('issue_id'),
            $request->getAttribute('category'),
        );
        return $document
            ? new JsonResponse($document, 200)
            : new EmptyResponse(404);
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true);
        $issue = [
            ...$body,
            'issue_id' => $request->getAttribute('issue_id'),
            'category' => $request->getAttribute('category'),
            'assembly' => [
                ...$body['assembly'],
                'assembly_id' => $request->getAttribute('assembly_id')
            ]
        ];

        $result = $this->issueService->store($issue);

        //TODO  if $result = 2, then...
        // Update embedded objects
        $this->plenaryAgendaService->updateIssue($issue);

        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setIssueService(Service\Issue $issue): self
    {
        $this->issueService = $issue;
        return $this;
    }

    public function setPlenaryAgendaService(Service\PlenaryAgenda $plenaryAgenda): self
    {
        $this->plenaryAgendaService = $plenaryAgenda;
        return $this;
    }
}
