<?php

namespace App\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceMinistryAware;

class Ministry implements
    RequestHandlerInterface,
    ServiceMinistryAware
{
    use HandlerTrait;

    private Service\Ministry $ministryService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $ministry = $this->ministryService->get($request->getAttribute('ministry_id'));

        return $ministry
            ? new JsonResponse($ministry, 200)
            : new EmptyResponse(404);
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->ministryService->store([
            ...json_decode($request->getBody()->getContents(), true),
            ...['ministry_id' => (int) $request->getAttribute('ministry_id')]
        ]);

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
}
