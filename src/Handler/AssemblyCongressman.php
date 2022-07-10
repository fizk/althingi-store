<?php

namespace App\Handler;

use App\Decorator\ServiceCongressmanSittingAware;
use App\Service\CongressmanSitting;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AssemblyCongressman implements RequestHandlerInterface, ServiceCongressmanSittingAware
{
    private CongressmanSitting $congressmanSitting;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $document = $this->congressmanSitting->getCongressmanAndAssembly(
            $request->getAttribute('assembly_id'),
            $request->getAttribute('congressman_id'),
        );
        return $document
            ? new JsonResponse($document, 200)
            : new EmptyResponse(404)
            ;
    }

    public function setCongressmanSittingService(CongressmanSitting $congressmanSitting): self
    {
        $this->congressmanSitting = $congressmanSitting;
        return $this;
    }
}
