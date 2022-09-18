<?php

namespace App\Handler;

use App\Service;
use App\Handler\{HandlerTrait, QueryParamTrait};
use App\Decorator\{ServiceCongressmanSittingAware};
use Laminas\Diactoros\Response\{JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class AssemblySittings implements
    RequestHandlerInterface,
    ServiceCongressmanSittingAware
{
    use HandlerTrait;
    use QueryParamTrait;

    private Service\CongressmanSitting $congressmanSittingService;

    /**
     * @query tegund | thingmenn, varamenn
     */
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $parties = $this->congressmanSittingService
            ->fetchCongressmenSessions(
                $request->getAttribute('assembly_id'),
                $this->extractCongressmanIsPrimary($request)
            );

        return new JsonResponse($parties, 200);
    }

    public function setCongressmanSittingService(Service\CongressmanSitting $congressmanSitting): self
    {
        $this->congressmanSittingService = $congressmanSitting;
        return $this;
    }
}
