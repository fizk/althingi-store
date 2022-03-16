<?php

use Fizk\Router\Route;

use App\Handler;

return (new Route('root', '', []))
    ->addRoute(
        (new Route('loggjafarthing', '/loggjafarthing', ['handler' => Handler\Assemblies::class]))
            ->addRoute(
                (new Route('loggjafarthing.item', '/(?<assembly_id>\d+)', ['handler' => Handler\Assembly::class])))
            )
    ->addRoute(
        (new Route('raduneyti', '/raduneyti', ['handler' => handler\Ministries::class]))
            ->addRoute(
                (new Route('raduneyti.item', '/(?<ministry_id>\d+)', ['handler' => Handler\Ministry::class]))
            )
    )
    ->addRoute(
        (new Route('thingflokkar', '/thingflokkar', ['handler' => handler\Parties::class]))
            ->addRoute(
                (new Route('thingflokkar.item', '/(?<party_id>\d+)', ['handler' => Handler\Party::class]))
            )
    )
    ->addRoute(
        (new Route('nefndir', '/nefndir', ['handler' => handler\Committees::class]))
            ->addRoute(
                (new Route('nefndir.item', '/(?<committee_id>\d+)', ['handler' => Handler\Committee::class]))
            )
    )
    ->addRoute(
        (new Route('kjordaemi', '/kjordaemi', ['handler' => handler\Constituencies::class]))
            ->addRoute(
                (new Route('kjordaemi.item', '/(?<constituency_id>\d+)', ['handler' => Handler\Constituency::class]))
            )
    )
    ->addRoute(
        (new Route('verdbolga', '/verdbolga', ['handler' => handler\Inflations::class]))
            ->addRoute(
                (new Route('verdbolga.item', '/(?<id>\d+)', ['handler' => Handler\Inflation::class]))
            )
    )
    ->addRoute(
        (new Route('thingseta', '/thingseta', ['handler' => handler\CongressmanSittings::class]))
            ->addRoute(
                (new Route('thingseta.item', '/(?<session_id>\d+)', ['handler' => Handler\CongressmanSitting::class]))
            )
    )
    ->addRoute(
        (new Route('nefndarseta', '/nefndarseta', ['handler' => handler\CommitteeSittings::class]))
            ->addRoute(
                (new Route('nefndarseta.item', '/(?<committee_sitting_id>\d+)', ['handler' => Handler\CommitteeSitting::class]))
            )
    )
    ->addRoute(
        (new Route('thingmenn', '/thingmenn', ['handler' => handler\Congressmen::class]))
            ->addRoute(
                (new Route('thingmenn.item', '/(?<congressman_id>\d+)', ['handler' => Handler\Congressman::class]))
            )
    )
;
