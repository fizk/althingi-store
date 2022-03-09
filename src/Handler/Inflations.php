<?php

namespace App\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Laminas\Diactoros\Response\{JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\{ServiceInflationAware};

class Inflations implements
    RequestHandlerInterface,
    ServiceInflationAware
{
    use HandlerTrait;

    private Service\Inflation $inflationService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->inflationService->fetch(),
            200
        );
    }

    public function setInflationService(Service\Inflation $inflation): self
    {
        $this->inflationService = $inflation;
        return $this;
    }
}
