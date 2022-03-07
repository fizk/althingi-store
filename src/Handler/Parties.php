<?php

namespace App\Handler;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServicePartyAware;

class Parties implements
    RequestHandlerInterface,
    ServicePartyAware
{
    use HandlerTrait;

    private Service\Party $partyService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->partyService->fetch()
        );
    }

    public function setPartyService(Service\Party $party): self
    {
        $this->partyService = $party;
        return $this;
    }
}
