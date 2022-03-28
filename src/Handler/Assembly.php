<?php

namespace App\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{
    ServiceAssemblyAware,
    ServiceCommitteeAware,
    ServiceCommitteeSittingAware,
    ServiceCongressmanSittingAware,
    ServiceMinisterSittingAware,
    ServiceMinistryAware,
    ServicePresidentSittingAware
};

class Assembly implements
    RequestHandlerInterface,
    ServiceAssemblyAware,
    ServiceMinistryAware,
    ServiceCommitteeAware,
    ServiceCongressmanSittingAware,
    ServiceCommitteeSittingAware,
    ServiceMinisterSittingAware,
    ServicePresidentSittingAware
{
    use HandlerTrait;

    private Service\Assembly $assemblyService;
    private Service\Ministry $ministryService;
    private Service\Committee $committeeService;
    private Service\CongressmanSitting $congressmanSittingService;
    private Service\CommitteeSitting $committeeSittingService;
    private Service\MinisterSitting $ministerSittingService;
    private Service\PresidentSitting $presidentSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $assembly = $this->assemblyService->get($request->getAttribute('assembly_id'));

        return $assembly
            ? new JsonResponse($assembly, 200)
            : new EmptyResponse(404);
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $assembly = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['assembly_id' => (int) $request->getAttribute('assembly_id')]
        ];
        $result = $this->assemblyService->store($assembly);

        //TODO  if $result = 2, then...
        // Update embedded objects
        $this->ministryService->updateAssembly($assembly);
        $this->committeeService->updateAssembly($assembly);
        $this->congressmanSittingService->updateAssembly($assembly);
        $this->committeeSittingService->updateAssembly($assembly);
        $this->ministerSittingService->updateAssembly($assembly);
        $this->presidentSittingService->updateAssembly($assembly);

        return match($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setAssemblyService(Service\Assembly $assembly): self
    {
        $this->assemblyService = $assembly;
        return $this;
    }

    public function setMinistryService(Service\Ministry $ministry): self
    {
        $this->ministryService = $ministry;
        return $this;
    }

    public function setCommitteeService(Service\Committee $committee): self
    {
        $this->committeeService = $committee;
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
}
