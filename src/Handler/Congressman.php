<?php

namespace App\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceCommitteeSittingAware, ServiceCongressmanAware, ServiceCongressmanSittingAware};

class Congressman implements
    RequestHandlerInterface,
    ServiceCongressmanAware,
    ServiceCommitteeSittingAware,
    ServiceCongressmanSittingAware
{
    use HandlerTrait;

    private Service\Congressman $congressmanService;
    private Service\CommitteeSitting $committeeSittingService;
    private Service\CongressmanSitting $congressmanSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $assembly = $this->congressmanService->get($request->getAttribute('congressman_id'));

        return $assembly
            ? new JsonResponse($assembly, 200)
            : new EmptyResponse(404);
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
}
