<?php

namespace App\Handler;

use App\Decorator\ServiceConstituencyAware;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\{EmptyResponse, JsonResponse};
use App\Service;
use App\Handler\HandlerTrait;

class Constituency implements
    RequestHandlerInterface,
    ServiceConstituencyAware
{
    use HandlerTrait;

    private Service\Constituency $committeeService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $party = $this->constituencyService->get($request->getAttribute('constituency_id'));

        return $party
            ? new JsonResponse($party, 200)
            : new EmptyResponse(404);
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $party = [
            ...json_decode($request->getBody()->getContents(), true),
            ...['constituency_id' => (int) $request->getAttribute('constituency_id')]
        ];
        $result = $this->constituencyService->store($party);

        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setConstituencyService(Service\Constituency $constituency): self
    {
        $this->constituencyService = $constituency;
        return $this;
    }
}
