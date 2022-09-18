<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{
    ServiceCongressmanSittingAware,
    ServiceCommitteeSittingAware,
    ServiceConstituencyAware,
    ServiceMinisterSittingAware,
    ServicePresidentSittingAware,
    ServicePlenaryAgendaAware,
};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class Constituency implements
    RequestHandlerInterface,
    ServiceConstituencyAware,
    ServiceCongressmanSittingAware,
    ServiceCommitteeSittingAware,
    ServiceMinisterSittingAware,
    ServicePresidentSittingAware,
    ServicePlenaryAgendaAware
{
    use HandlerTrait;

    private Service\Constituency $committeeService;
    private Service\CongressmanSitting $congressmanSittingService;
    private Service\CommitteeSitting $committeeSittingService;
    private Service\MinisterSitting $ministerSittingService;
    private Service\PresidentSitting $presidentSittingService;
    private Service\PlenaryAgenda $plenaryAgendaService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $party = $this->constituencyService->get(
            $request->getAttribute('constituency_id')
        );

        return $party
            ? new JsonResponse($party, 200)
            : new EmptyResponse(404)
            ;
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $constituency = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['constituency_id' => (int) $request->getAttribute('constituency_id')]
        ];
        $result = $this->constituencyService->store($constituency);

        // if $result = 2, then...
        // Update embedded objects
        $this->congressmanSittingService->updateConstituency($constituency);
        $this->committeeSittingService->updateConstituency($constituency);
        $this->ministerSittingService->updateConstituency($constituency);
        $this->presidentSittingService->updateConstituency($constituency);
        $this->plenaryAgendaService->updateConstituency($constituency);

        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setConstituencyService(Service\Constituency $constituency): self
    {
        $this->constituencyService = $constituency;
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

    public function setMinisterSittingService(Service\MinisterSitting $ministerSitting): self
    {
        $this->ministerSittingService = $ministerSitting;
        return $this;
    }

    public function setPresidentSittingService(Service\PresidentSitting $presidentSitting): self
    {
        $this->presidentSittingService = $presidentSitting;
        return $this;
    }

    public function setPlenaryAgendaService(Service\PlenaryAgenda $plenaryAgenda): self
    {
        $this->plenaryAgendaService = $plenaryAgenda;
        return $this;

    }
}
