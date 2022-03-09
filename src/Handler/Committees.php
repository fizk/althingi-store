<?php

namespace App\Handler;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceCommitteeAware;

class Committees implements
    RequestHandlerInterface,
    ServiceCommitteeAware
{
    use HandlerTrait;

    private Service\Committee $committeeService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->committeeService->fetch(),
            200
        );
    }

    public function setCommitteeService(Service\Committee $committee): self
    {
        $this->committeeService = $committee;
        return $this;
    }
}
