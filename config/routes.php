<?php

use Fizk\Router\Route;

use App\Handler;

return (new Route('root', '', []))
    ->addRoute(
        (new Route('loggjafarthing', '/loggjafarthing', ['handler' => Handler\Assemblies::class]))
            ->addRoute(
                new Route('loggjafarthing.item', '/(?<assembly_id>\d+)', ['handler' => Handler\Assembly::class]))
            )
    ->addRoute(
        (new Route('raduneyti', '/raduneyti', ['handler' => handler\Ministries::class]))
            ->addRoute(
                (new Route('raduneyti.item', '/(?<ministry_id>\d+)', ['handler' => Handler\Ministry::class]))
            )
    )
;
