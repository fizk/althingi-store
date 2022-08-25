<?php

namespace App\Handler;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceSpeechAware;

class AssemblyIssueSpeeches implements
    RequestHandlerInterface,
    ServiceSpeechAware
{
    use HandlerTrait;

    private Service\Speech $speechService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        return new JsonResponse(
            $this->speechService->fetchByIssue(
                $request->getAttribute('assembly_id'),
                $request->getAttribute('issue_id'),
                $request->getAttribute('category'),
                isset($params['bendill']) ? $params['bendill'] : null
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
