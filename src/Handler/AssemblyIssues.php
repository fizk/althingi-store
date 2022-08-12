<?php

namespace App\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Laminas\Diactoros\Response\{JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceIssueAware};

class AssemblyIssues implements
    RequestHandlerInterface,
    ServiceIssueAware
{
    use HandlerTrait;

    private Service\Issue $issueService;
    private Service\PlenaryAgenda $plenaryAgendaService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $documents = $this->issueService->fetchByAssembly(
            $request->getAttribute('assembly_id'),
            $request->getAttribute('category'),
            $request->getQueryParams(),
        );
        return new JsonResponse($documents, 200);
    }

    public function setIssueService(Service\Issue $issue): self
    {
        $this->issueService = $issue;
        return $this;
    }
}
