<?php

namespace App\Handler;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceMinisterSittingAware;

class MinisterSittings implements
    RequestHandlerInterface,
    ServiceMinisterSittingAware
{
    use HandlerTrait;

    private Service\MinisterSitting $ministerSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->ministerSittingService->fetch(),
            200
        );
    }

    public function setMinisterSittingService(Service\MinisterSitting $ministerSitting): self
    {
        $this->ministerSittingService = $ministerSitting;
        return $this;
    }
}
