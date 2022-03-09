<?php

namespace App\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceMinistryAware;

class Ministries implements
    RequestHandlerInterface,
    ServiceMinistryAware
{
    use HandlerTrait;

    private Service\Ministry $ministryService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->ministryService->fetch(),
            200
        );
    }

    public function setMinistryService(Service\Ministry $ministry): self
    {
        $this->ministryService = $ministry;
        return $this;
    }
}
