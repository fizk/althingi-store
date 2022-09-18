<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceAssemblyAware;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class Assemblies implements
    RequestHandlerInterface,
    ServiceAssemblyAware
{
    use HandlerTrait;

    private Service\Assembly $assemblyService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->assemblyService->fetch(),
            200
        );
    }

    public function setAssemblyService(Service\Assembly $assembly): self
    {
        $this->assemblyService = $assembly;
        return $this;
    }
}
