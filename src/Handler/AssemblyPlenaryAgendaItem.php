<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServicePlenaryAgendaAware};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class AssemblyPlenaryAgendaItem implements
    RequestHandlerInterface,
    ServicePlenaryAgendaAware
{
    use HandlerTrait;

    private Service\PlenaryAgenda $plenaryAgendaService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $agenda = $this->plenaryAgendaService->get(
            $request->getAttribute('assembly_id'),
            $request->getAttribute('plenary_id'),
            $request->getAttribute('item_id')
        );

        return $agenda
            ? new JsonResponse($agenda, 200)
            : new EmptyResponse(404)
            ;
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $agenda = (array) json_decode($request->getBody()->getContents(), true);
        $result = $this->plenaryAgendaService->store([
            ...$agenda,
            'item_id' => $request->getAttribute('item_id'),
            'assembly' => [
                ...$agenda['assembly'],
                'assembly_id' => $request->getAttribute('assembly_id')
            ],
            'plenary' => [
                ...$agenda['plenary'],
                'plenary_id' => $request->getAttribute('plenary_id')
            ]
        ]);

        return match($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setPlenaryAgendaService(Service\PlenaryAgenda $plenaryAgenda): self
    {
        $this->plenaryAgendaService = $plenaryAgenda;
        return $this;
    }
}
