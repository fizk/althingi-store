<?php

namespace App\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Laminas\Diactoros\Response\{JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServicePlenaryAware};

class AssemblyPlenaries implements
    RequestHandlerInterface,
    ServicePlenaryAware
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

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $plenary = $this->plenaryService->fetchByAssembly($request->getAttribute('assembly_id'));
        return new JsonResponse($plenary, 200);
    }

    public function setPlenaryService(Service\Plenary $plenary): self
    {
        $this->plenaryService = $plenary;
        return $this;
    }
}
