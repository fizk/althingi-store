<?php

namespace App\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Laminas\Diactoros\Response\{JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{
    ServicePresidentSittingAware
};

class PresidentSittings implements
    RequestHandlerInterface,
    ServicePresidentSittingAware
{
    use HandlerTrait;

    private Service\PresidentSitting $presidentSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $presidentSittings = $this->presidentSittingService->fetch();

        return new JsonResponse($presidentSittings, 200);
    }

    public function setPresidentSittingService(service\PresidentSitting $presidentSitting): self
    {
        $this->presidentSittingService = $presidentSitting;
        return $this;
    }
}
