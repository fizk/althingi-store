<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceMinisterSittingAware};
use Laminas\Diactoros\Response\{JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class AssemblyGovernmentMinistries implements
    RequestHandlerInterface,
    ServiceMinisterSittingAware
{
    use HandlerTrait;

    private Service\MinisterSitting $ministerSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $sessions = $this->ministerSittingService
            ->fetchGovernmentSessionsByAssembly($request->getAttribute('assembly_id'));

        return new JsonResponse($sessions, 200);
    }

    public function setMinisterSittingService(Service\MinisterSitting $ministerSitting): self
    {
        $this->ministerSittingService = $ministerSitting;
        return $this;
    }
}
