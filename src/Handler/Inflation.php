<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceInflationAware};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class Inflation implements
    RequestHandlerInterface,
    ServiceInflationAware
{
    use HandlerTrait;

    private Service\Inflation $inflationService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $inflation = $this->inflationService->get(
            $request->getAttribute('id')
        );

        return $inflation
            ? new JsonResponse($inflation, 200)
            : new EmptyResponse(404)
            ;
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $inflation = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['id' => (int) $request->getAttribute('id')]
        ];
        $result = $this->inflationService->store($inflation);

        return match($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setInflationService(Service\Inflation $inflation): self
    {
        $this->inflationService = $inflation;
        return $this;
    }
}
