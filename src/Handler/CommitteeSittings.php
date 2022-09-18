<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceCommitteeSittingAware;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class CommitteeSittings implements
    RequestHandlerInterface,
    ServiceCommitteeSittingAware
{
    use HandlerTrait;

    private Service\CommitteeSitting $committeeSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->committeeSittingService->fetch(),
            200
        );
    }

    public function setCommitteeSittingService(Service\CommitteeSitting $committeeSitting): self
    {
        $this->committeeSittingService = $committeeSitting;
        return $this;
    }
}
