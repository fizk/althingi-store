<?php

namespace App\Handler;

use App\Service;
use App\Decorator\ServiceSpeechAware;
use App\Handler\{HandlerTrait, QueryParamTrait};
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class AssemblyIssueSpeeches implements
    RequestHandlerInterface,
    ServiceSpeechAware
{
    use HandlerTrait;
    use QueryParamTrait;

    private Service\Speech $speechService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->speechService->fetchByIssue(
                $request->getAttribute('assembly_id'),
                $request->getAttribute('issue_id'),
                $request->getAttribute('category'),
                $this->extractPointer($request)
            ),
            200
        );
    }

    public function setSpeechService(Service\Speech $speech): self
    {
        $this->speechService = $speech;
        return $this;
    }
}
