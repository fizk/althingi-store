<?php

namespace App\Handler;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServicePresidentSittingAware;
use Laminas\Diactoros\Response\EmptyResponse;

class PresidentSitting implements
    RequestHandlerInterface,
    ServicePresidentSittingAware
{
    use HandlerTrait;

    private Service\PresidentSitting $presidentSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $presidentSitting = $this->presidentSittingService
            ->get($request->getAttribute('president_id'));

        return $presidentSitting
            ? new JsonResponse($presidentSitting, 200)
            : new EmptyResponse(404);
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $assembly = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['president_id' => (int) $request->getAttribute('president_id')]
        ];
        $result = $this->presidentSittingService->store($assembly);

        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setPresidentSittingService(Service\PresidentSitting $presidentSitting): self
    {
        $this->presidentSittingService = $presidentSitting;
        return $this;
    }
}
