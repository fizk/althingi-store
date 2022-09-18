<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceMinisterSittingAware;
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class MinisterSitting implements
    RequestHandlerInterface,
    ServiceMinisterSittingAware
{
    use HandlerTrait;

    private Service\MinisterSitting $ministerSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $ministerSitting = $this->ministerSittingService
            ->get($request->getAttribute('minister_sitting_id'));

        return $ministerSitting
            ? new JsonResponse($ministerSitting, 200)
            : new EmptyResponse(404)
            ;
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $ministerSitting = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['minister_sitting_id' => (int) $request->getAttribute('minister_sitting_id')]
        ];
        $result = $this->ministerSittingService->store($ministerSitting);

        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setMinisterSittingService(Service\MinisterSitting $ministerSitting): self
    {
        $this->ministerSittingService = $ministerSitting;
        return $this;
    }
}
