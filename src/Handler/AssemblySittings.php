<?php

namespace App\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Laminas\Diactoros\Response\{JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceCongressmanSittingAware};

class AssemblySittings implements
    RequestHandlerInterface,
    ServiceCongressmanSittingAware
{
    use HandlerTrait;

    private Service\CongressmanSitting $congressmanSittingService;

    /**
     * @query tegund | thingmenn, varamenn
     */
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $type = true;
        if (array_key_exists('tegund', $params)) {
            $type = $params['tegund'] === 'varamenn' ? false : true;
        }
        $parties = $this->congressmanSittingService
            ->fetchCongressmenSessions($request->getAttribute('assembly_id'), $type);

        return new JsonResponse($parties, 200);
    }

    public function setCongressmanSittingService(Service\CongressmanSitting $congressmanSitting): self
    {
        $this->congressmanSittingService = $congressmanSitting;
        return $this;
    }
}
