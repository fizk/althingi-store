<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceCongressmanAware;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class Congressmen implements
    RequestHandlerInterface,
    ServiceCongressmanAware
{
    use HandlerTrait;

    private Service\Congressman $congressmanService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->congressmanService->fetch(),
            200
        );
    }

    public function setCongressmanService(Service\Congressman $congressman): self
    {
        $this->congressmanService = $congressman;
        return $this;
    }
}
