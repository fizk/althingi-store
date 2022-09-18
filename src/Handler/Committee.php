<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceCommitteeSittingAware, ServiceCommitteeAware};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class Committee implements
    RequestHandlerInterface,
    ServiceCommitteeAware,
    ServiceCommitteeSittingAware
{
    use HandlerTrait;

    private Service\Committee $committeeService;
    private Service\CommitteeSitting $committeeSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $party = $this->committeeService->get(
            $request->getAttribute('committee_id')
        );

        return $party
            ? new JsonResponse($party, 200)
            : new EmptyResponse(404)
            ;
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $committee = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['committee_id' => (int) $request->getAttribute('committee_id')]
        ];
        $result = $this->committeeService->store($committee);

        //TODO  if $result = 2, then...
        // Update embedded objects
        $this->committeeSittingService->updateCommittee($committee);

        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setCommitteeService(Service\Committee $committee): self
    {
        $this->committeeService = $committee;
        return $this;
    }

    public function setCommitteeSittingService(Service\CommitteeSitting $committeeSitting): self
    {
        $this->committeeSittingService = $committeeSitting;
        return $this;
    }
}
