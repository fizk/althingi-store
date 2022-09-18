<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceAssemblyAware, ServiceInflationAware};
use Laminas\Diactoros\Response\{JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use DateTime;

class AssemblyInflation implements
    RequestHandlerInterface,
    ServiceInflationAware,
    ServiceAssemblyAware
{
    use HandlerTrait;

    private Service\Inflation $inflationService;
    private Service\Assembly $assemblyService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $assembly = $this->assemblyService->get($request->getAttribute('assembly_id'));
        if (!$assembly) {
            return new JsonResponse([]);
        }

        $inflation = $this->inflationService->fetchRange(
            new DateTime($assembly['from']),
            $assembly['to'] ? new DateTime($assembly['to']) : new DateTime(),
        );

        return new JsonResponse($inflation, 200);
    }

    public function setInflationService(Service\Inflation $inflation): self
    {
        $this->inflationService = $inflation;
        return $this;
    }

    public function setAssemblyService(Service\Assembly $assembly): self
    {
        $this->assemblyService = $assembly;
        return $this;
    }
}
