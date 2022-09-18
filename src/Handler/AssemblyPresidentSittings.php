<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServicePresidentSittingAware;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class AssemblyPresidentSittings implements
    RequestHandlerInterface,
    ServicePresidentSittingAware
{
    use HandlerTrait;

    private Service\PresidentSitting $presidentSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->presidentSittingService->fetchByAssembly(
                $request->getAttribute('assembly_id')
            ),
            200
        );
    }

    public function setPresidentSittingService(Service\PresidentSitting $presidentSitting): self
    {
        $this->presidentSittingService = $presidentSitting;
        return $this;
    }
}
