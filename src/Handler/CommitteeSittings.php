<?php

namespace App\Handler;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceCommitteeSittingAware;
use Exception;

class CommitteeSittings implements
    RequestHandlerInterface,
    ServiceCommitteeSittingAware
{
    use HandlerTrait;

    private Service\CommitteeSitting $committeeSittingService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        throw new Exception();
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
