<?php

namespace App\Handler;

use App\Service;
use App\Decorator\{ServiceIssueAware};
use App\Handler\{HandlerTrait, QueryParamTrait};
use Laminas\Diactoros\Response\{JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class AssemblyContentCategories implements
    RequestHandlerInterface,
    ServiceIssueAware
{
    use HandlerTrait;
    use QueryParamTrait;

    private Service\Issue $issueService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $assembly = $this->issueService
            ->fetchContentCategoriesAggregation(
                $request->getAttribute('assembly_id'),
                $this->extractType($request)
            );
        return new JsonResponse($assembly, 200);
    }

    public function setIssueService(Service\Issue $issue): self
    {
        $this->issueService = $issue;
        return $this;
    }

}
