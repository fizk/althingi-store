<?php

namespace App\Handler;

use App\Decorator\ServiceCommitteeSittingAware;
use App\Decorator\ServiceCongressmanSittingAware;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServicePartyAware;

class Party implements
    RequestHandlerInterface,
    ServicePartyAware,
    ServiceCongressmanSittingAware,
    ServiceCommitteeSittingAware
{
    use HandlerTrait;

    private Service\Party $partyService;
    private Service\CongressmanSitting $congressmanSittingService;
    private Service\CommitteeSitting $committeeSittingService;

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

        // @todo if $result = 2, then...
        // Update embedded objects
        $this->congressmanSittingService->updateParty($party);
        $this->committeeSittingService->updateParty($party);

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

    public function setCongressmanSittingService(Service\CongressmanSitting $congressmanSitting): self
    {
        $this->congressmanSittingService = $congressmanSitting;
        return $this;
    }

    public function setCommitteeSittingService(Service\CommitteeSitting $committeeSitting): self
    {
        $this->committeeSittingService = $committeeSitting;
        return $this;
    }
}
