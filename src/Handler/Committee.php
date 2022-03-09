<?php

namespace App\Handler;

use App\Decorator\ServiceCommitteeAware;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;

class Committee implements
    RequestHandlerInterface,
    ServiceCommitteeAware
{
    use HandlerTrait;

    private Service\Committee $committeeService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $party = $this->committeeService->get($request->getAttribute('committee_id'));

        return $party
            ? new JsonResponse($party, 200)
            : new EmptyResponse(404);
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $party = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['committee_id' => (int) $request->getAttribute('committee_id')]
        ];
        $result = $this->committeeService->store($party);

        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setCommitteeService(Service\Committee $committee): self
    {
        $this->committeeService = $committee;
        return $this;
    }
}
