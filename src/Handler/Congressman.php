<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{
    ServiceCommitteeSittingAware,
    ServiceCongressmanAware,
    ServiceCongressmanSittingAware,
    ServiceMinisterSittingAware,
    ServicePresidentSittingAware,
    ServicePlenaryAgendaAware,
};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

class Congressman implements
    RequestHandlerInterface,
    ServiceCongressmanAware,
    ServiceCommitteeSittingAware,
    ServiceCongressmanSittingAware,
    ServiceMinisterSittingAware,
    ServicePresidentSittingAware,
    ServicePlenaryAgendaAware
{
    use HandlerTrait;

    private Service\Congressman $congressmanService;
    private Service\CommitteeSitting $committeeSittingService;
    private Service\CongressmanSitting $congressmanSittingService;
    private Service\MinisterSitting $ministerSittingService;
    private Service\PresidentSitting $presidentSittingService;
    private Service\PlenaryAgenda $plenaryAgendaService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $assembly = $this->congressmanService->get(
            $request->getAttribute('congressman_id')
        );

        return $assembly
            ? new JsonResponse($assembly, 200)
            : new EmptyResponse(404)
            ;
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $congressman = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['congressman_id' => (int) $request->getAttribute('congressman_id')]
        ];
        $result = $this->congressmanService->store($congressman);

        // update embedded objects
        $this->committeeSittingService->updateCongressman($congressman);
        $this->congressmanSittingService->updateCongressman($congressman);
        $this->ministerSittingService->updateCongressman($congressman);
        $this->presidentSittingService->updateCongressman($congressman);
        $this->plenaryAgendaService->updateCongressman($congressman);

        return match($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setCongressmanService(Service\Congressman $congressman): self
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    public function setCommitteeSittingService(Service\CommitteeSitting $committeeSitting): self
    {
        $this->committeeSittingService = $committeeSitting;
        return $this;
    }

    public function setCongressmanSittingService(Service\CongressmanSitting $congressmanSitting): self
    {
        $this->congressmanSittingService = $congressmanSitting;
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
