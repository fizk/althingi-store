<?php

namespace App\Routes;

use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Uri;
use \Fizk\Router;

trait RoutesTrait {

    private $routes;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->routes = require('./config/routes.php');
    }

    public function getRoutesDefinitions(): Router\Route
    {
        return $this->routes;
    }

    public function createRequest(string $uri): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest(
            'GET',
            new Uri($uri)
        );
    }
}
