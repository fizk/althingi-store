<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServicePlenaryAgendaAware};
use Laminas\Diactoros\Response\{JsonResponse};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class AssemblyPlenaryAgenda implements
    RequestHandlerInterface,
    ServicePlenaryAgendaAware
{
    use HandlerTrait;

    private Service\PlenaryAgenda $plenaryAgendaService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $agenda = $this->plenaryAgendaService->fetchByPlenary(
            $request->getAttribute('assembly_id'),
            $request->getAttribute('plenary_id')
        );

        return new JsonResponse($agenda, 200);
    }

    public function setPlenaryAgendaService(Service\PlenaryAgenda $plenaryAgenda): self
    {
        $this->plenaryAgendaService = $plenaryAgenda;
        return $this;
    }
}
