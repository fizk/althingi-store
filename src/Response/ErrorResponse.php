<?php

namespace App\Response;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\RequestInterface;
use Throwable;

class ErrorResponse extends JsonResponse
{
    public function __construct(
        Throwable $error,
        RequestInterface $request,
        int $status = 500,
        array $headers = [],
        int $encodingOptions = self::DEFAULT_JSON_FLAGS)
    {
        parent::__construct([
            'request' => $request->getUri()->__toString(),
            'message' => $error->getMessage(),
            'path' => "{$error->getFile()}:{$error->getLine()}",
            'trace' => $error->getTrace()
        ], $status, $headers, $encodingOptions);
    }
}
