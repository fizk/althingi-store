<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceCongressmanSittingAware};
use Laminas\Diactoros\Response\{JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class AssemblyParties implements
    RequestHandlerInterface,
    ServiceCongressmanSittingAware
{
    use HandlerTrait;

    private Service\CongressmanSitting $congressmanSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $parties = $this->congressmanSittingService
            ->fetchPartiesByAssembly($request->getAttribute('assembly_id'));

        return new JsonResponse($parties, 200);
    }

    public function setCongressmanSittingService(Service\CongressmanSitting $congressmanSitting): self
    {
        $this->congressmanSittingService = $congressmanSitting;
        return $this;
    }
}
