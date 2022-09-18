<?php

namespace App\Handler;

use App\Service;
use App\Handler\{HandlerTrait, QueryParamTrait};
use App\Decorator\{ServiceIssueAware};
use Laminas\Diactoros\Response\{JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class AssemblyIssues implements
    RequestHandlerInterface,
    ServiceIssueAware
{
    use HandlerTrait;
    use QueryParamTrait;

    private Service\Issue $issueService;
    private Service\PlenaryAgenda $plenaryAgendaService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $documents = $this->issueService->fetchByAssembly(
            $request->getAttribute('assembly_id'),
            $request->getAttribute('category'),
            $this->extractPointer($request),
            $this->extractType($request),
        );
        return new JsonResponse($documents, 200);
    }

    public function setIssueService(Service\Issue $issue): self
    {
        $this->issueService = $issue;
        return $this;
    }
}
