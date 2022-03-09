<?php

namespace App\Handler;

use App\Decorator\ServiceConstituencyAware;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\{JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;

class Constituencies implements
    RequestHandlerInterface,
    ServiceConstituencyAware
{
    use HandlerTrait;

    private Service\Constituency $constituencyService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->constituencyService->fetch(),
            200
        );
    }

    public function setConstituencyService(Service\Constituency $constituency): self
    {
        $this->constituencyService = $constituency;
        return $this;
    }
}
