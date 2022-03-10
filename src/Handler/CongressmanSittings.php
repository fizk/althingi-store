<?php

namespace App\Handler;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceCongressmanSittingAware;

class CongressmanSittings implements
    RequestHandlerInterface,
    ServiceCongressmanSittingAware
{
    use HandlerTrait;

    private Service\CongressmanSitting $congressmanSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->congressmanSittingService->fetch(),
            200
        );
    }

    public function setCongressmanSittingService(Service\CongressmanSitting $congressmanSitting): self
    {
        $this->congressmanSittingService = $congressmanSitting;
        return $this;
    }
}
