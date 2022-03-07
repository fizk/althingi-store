<?php

namespace App\Handler;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServicePartyAware;

class Party implements
    RequestHandlerInterface,
    ServicePartyAware
{
    use HandlerTrait;

    private Service\Party $partyService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $party = $this->partyService->get($request->getAttribute('party_id'));

        return $party
            ? new JsonResponse($party, 200)
            : new EmptyResponse(404);
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $party = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['party_id' => (int) $request->getAttribute('party_id')]
        ];
        $result = $this->partyService->store($party);

        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setPartyService(Service\Party $party): self
    {
        $this->partyService = $party;
        return $this;
    }
}
