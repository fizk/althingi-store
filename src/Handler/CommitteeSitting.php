<?php

namespace App\Handler;

use App\Decorator\ServiceCommitteeSittingAware;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;

class CommitteeSitting implements
    RequestHandlerInterface,
    ServiceCommitteeSittingAware
{
    use HandlerTrait;

    private Service\CommitteeSitting $committeeSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $party = $this->committeeSittingService->get($request->getAttribute('committee_sitting_id'));

        return $party
            ? new JsonResponse($party, 200)
            : new EmptyResponse(404);
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $party = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['committee_sitting_id' => (int) $request->getAttribute('committee_sitting_id')]
        ];
        $result = $this->committeeSittingService->store($party);

        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setCommitteeSittingService(Service\CommitteeSitting $committeeSitting): self
    {
        $this->committeeSittingService = $committeeSitting;
        return $this;
    }
}
