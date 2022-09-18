<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{
    ServiceMinisterSittingAware,
    ServiceMinistryAware
};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class Ministry implements
    RequestHandlerInterface,
    ServiceMinistryAware,
    ServiceMinisterSittingAware
{
    use HandlerTrait;

    private Service\Ministry $ministryService;
    private Service\MinisterSitting $ministerSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $ministry = $this->ministryService->get(
            $request->getAttribute('ministry_id')
        );

        return $ministry
            ? new JsonResponse($ministry, 200)
            : new EmptyResponse(404)
            ;
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $ministry = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['ministry_id' => (int) $request->getAttribute('ministry_id')]
        ];
        $result = $this->ministryService->store($ministry);

        //TODO  if $result = 2, then...
        // Update embedded objects
        $this->ministerSittingService->updateMinistry($ministry);

        return match($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setMinistryService(Service\Ministry $ministry): self
    {
        $this->ministryService = $ministry;
        return $this;
    }

    public function setMinisterSittingService(Service\MinisterSitting $ministerSitting): self
    {
        $this->ministerSittingService = $ministerSitting;
        return $this;
    }
}
