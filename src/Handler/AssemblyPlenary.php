<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{
    ServicePlenaryAgendaAware,
    ServicePlenaryAware
};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class AssemblyPlenary implements
    RequestHandlerInterface,
    ServicePlenaryAware,
    ServicePlenaryAgendaAware
{
    use HandlerTrait;

    private Service\Assembly $assemblyService;
    private Service\Ministry $ministryService;
    private Service\Committee $committeeService;
    private Service\CongressmanSitting $congressmanSittingService;
    private Service\CommitteeSitting $committeeSittingService;
    private Service\MinisterSitting $ministerSittingService;
    private Service\PresidentSitting $presidentSittingService;
    private Service\Plenary $plenaryService;
    private Service\PlenaryAgenda $plenaryAgendaService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $plenaryId = $request->getAttribute('plenary_id');
        $plenary = $this->plenaryService->get(
            $request->getAttribute('assembly_id'),
            is_numeric($plenaryId) ? $plenaryId : null
        );

        return $plenary
            ? new JsonResponse($plenary, 200)
            : new EmptyResponse(404)
            ;
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $plenary = [
            ...json_decode($request->getBody()->getContents(), true),
            ...[
                'plenary_id' => (int) $request->getAttribute('plenary_id'),
                'assembly_id' => (int) $request->getAttribute('assembly_id'),
            ]
        ];
        $result = $this->plenaryService->store($plenary);

        // Update embedded objects
        $this->plenaryAgendaService->updatePlenary($plenary);

        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setPlenaryService(Service\Plenary $plenary): self
    {
        $this->plenaryService = $plenary;
        return $this;
    }

    public function setPlenaryAgendaService(Service\PlenaryAgenda $plenaryAgenda): self
    {
        $this->plenaryAgendaService = $plenaryAgenda;
        return $this;
    }
}
