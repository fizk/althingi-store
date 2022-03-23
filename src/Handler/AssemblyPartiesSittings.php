<?php

namespace App\Handler;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceCongressmanSittingAware;

class AssemblyPartiesSittings implements
    RequestHandlerInterface,
    ServiceCongressmanSittingAware
{
    use HandlerTrait;

    private Service\CongressmanSitting $congressmanSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $type = true;
        if (array_key_exists('tegund', $params)) {
            $type = $params['tegund'] === 'varamenn' ? false : true;
        }
        return new JsonResponse(
            $this->congressmanSittingService->fetchPartiesSessions(
                $request->getAttribute('assembly_id'),
                $type
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
