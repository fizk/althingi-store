<?php

namespace App\Handler;

use App\Service;
use App\Handler\{HandlerTrait, QueryParamTrait};
use App\Decorator\ServiceCongressmanSittingAware;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class AssemblyPartiesSittings implements
    RequestHandlerInterface,
    ServiceCongressmanSittingAware
{
    use HandlerTrait;
    use QueryParamTrait;

    private Service\CongressmanSitting $congressmanSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->congressmanSittingService->fetchPartiesSessions(
                $request->getAttribute('assembly_id'),
                $this->extractCongressmanIsPrimary($request)
            ),
            200
        );
    }

    public function setCongressmanSittingService(Service\CongressmanSitting $congressmanSitting): self
    {
        $this->congressmanSittingService = $congressmanSitting;
        return $this;
    }
}
