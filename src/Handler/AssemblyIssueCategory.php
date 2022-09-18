<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceIssueAware};
use Laminas\Diactoros\Response\{EmptyResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class AssemblyIssueCategory implements
    RequestHandlerInterface,
    ServiceIssueAware
{
    use HandlerTrait;

    private Service\Issue $issueService;
    private Service\PlenaryAgenda $plenaryAgendaService;

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true);

        $result = $this->issueService->addContentCategory(
            $request->getAttribute('assembly_id'),
            $request->getAttribute('issue_id'),
            $request->getAttribute('category'),
            $body
        );

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
}
