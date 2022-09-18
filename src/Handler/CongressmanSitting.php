<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceCongressmanSittingAware;
use Laminas\Diactoros\Response\{JsonResponse, EmptyResponse};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class CongressmanSitting implements
    RequestHandlerInterface,
    ServiceCongressmanSittingAware
{
    use HandlerTrait;

    private Service\CongressmanSitting $congressmanSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $congressmanSitting = $this->congressmanSittingService
            ->get($request->getAttribute('session_id'));

        return $congressmanSitting
            ? new JsonResponse($congressmanSitting, 200)
            : new EmptyResponse(404)
            ;
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $congressmanSitting = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['session_id' => (int) $request->getAttribute('session_id')]
        ];
        $result = $this->congressmanSittingService->store($congressmanSitting);

        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setCongressmanSittingService(Service\CongressmanSitting $congressmanSitting): self
    {
        $this->congressmanSittingService = $congressmanSitting;
        return $this;
    }
}
