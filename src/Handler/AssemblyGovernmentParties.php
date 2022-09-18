<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceMinisterSittingAware};
use Laminas\Diactoros\Response\{JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class AssemblyGovernmentParties implements
    RequestHandlerInterface,
    ServiceMinisterSittingAware
{
    use HandlerTrait;

    private Service\MinisterSitting $ministerSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $parties = $this->ministerSittingService
            ->fetchGovernmentPartiesByAssembly($request->getAttribute('assembly_id'));

        return new JsonResponse($parties, 200);
    }

    public function setMinisterSittingService(Service\MinisterSitting $ministerSitting): self
    {
        $this->ministerSittingService = $ministerSitting;
        return $this;
    }
}
