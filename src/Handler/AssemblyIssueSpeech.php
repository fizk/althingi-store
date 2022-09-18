<?php

namespace App\Handler;

use App\Service;
use App\Handler\HandlerTrait;
use App\Decorator\ServiceSpeechAware;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

class AssemblyIssueSpeech implements
    RequestHandlerInterface,
    ServiceSpeechAware
{
    use HandlerTrait;

    private Service\Speech $speechService;

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $request->getAttribute('assembly_id');
        $request->getAttribute('issue_id');
        $request->getAttribute('category');
        $request->getAttribute('speech_id');

        return new JsonResponse(
            $this->speechService->fetch(),
            200
        );
    }

    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true);
        $speech = [
            ...$body,
            'assembly' => [
                ...$body['assembly'],
                'assembly_id' => (int) $request->getAttribute('assembly_id')
            ],
            'issue' => [
                ...$body['issue'],
                'issue_id' => (int) $request->getAttribute('issue_id'),
                'category' => $request->getAttribute('category')
            ],
            'speech_id' => $request->getAttribute('speech_id'),
        ];
        $result = $this->speechService->store($speech);
        return match ($result) {
            1 => new EmptyResponse(201),
            2 => new EmptyResponse(205),
            default => new EmptyResponse(304),
        };
    }

    public function setSpeechService(Service\Speech $speech): self
    {
        $this->speechService = $speech;
        return $this;
    }
}
