<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServicePresidentSittingAware};
use Laminas\Diactoros\Response\{JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class PresidentSittings implements
    RequestHandlerInterface,
    ServicePresidentSittingAware
{
    use HandlerTrait;

    private Service\PresidentSitting $presidentSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->presidentSittingService->fetch(),
            200
        );
    }

    public function setPresidentSittingService(service\PresidentSitting $presidentSitting): self
    {
        $this->presidentSittingService = $presidentSitting;
        return $this;
    }
}
