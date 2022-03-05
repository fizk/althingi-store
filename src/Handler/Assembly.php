<?php

namespace App\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceAssemblyAware;

class Assembly implements
    RequestHandlerInterface,
    ServiceAssemblyAware
{
    use HandlerTrait;

    private Service\Assembly $assemblyService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $assembly = $this->assemblyService->get($request->getAttribute('assembly_id'));

        return $assembly
            ? new JsonResponse($assembly, 200)
            : new EmptyResponse(404);
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->assemblyService->store([
            ...json_decode($request->getBody()->getContents(), true),
            ...['assembly_id' => (int) $request->getAttribute('assembly_id')]
        ]);

        return match($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setAssemblyService(Service\Assembly $assembly): self
    {
        $this->assemblyService = $assembly;
        return $this;
    }
}
