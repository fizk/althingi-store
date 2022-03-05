<?php

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\EmptyResponse;

trait HandlerTrait
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return match(strtolower($request->getMethod())) {
            'get' => $this->get($request),
            'put' => $this->put($request),
            'post' => $this->post($request),
            'head' => $this->head($request),
            'patch' => $this->patch($request),
            'delete' => $this->delete($request),
            'options' => $this->options($request),
            default => new EmptyResponse(501)
        };
    }

    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(501);
    }

    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(501);
    }

    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(501);
    }

    protected function patch(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(501);
    }

    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(501);
    }

    protected function head(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(501);
    }

    protected function options(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(501);
    }
}