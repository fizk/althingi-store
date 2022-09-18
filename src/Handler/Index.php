<?php

namespace App\Handler;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Laminas\Diactoros\Response\{HtmlResponse};
use Fizk\Router\RouteInterface;

class Index implements RequestHandlerInterface
{
    private ContainerInterface $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $string = $this->getList($this->container->get(RouteInterface::class), '');
        return new HtmlResponse('<ul>'.$string.'</ul>', 200);
    }

    private function getList($routes, $prefix)
    {
        $list = '';
        foreach ($routes as $route) {
            $list .= (
                '<li>' .
                    '<strong>'.htmlspecialchars($prefix . $route->getPattern()) . '</strong>' .
                    ('<ul><li><small>'. $route->getParams()['handler'] .'</small></li></ul>') .
                    $this->getList($route->getIterator(), $prefix . $route->getPattern()).
                '</li>'
            );
        }
        return $list . '';
    }
}
