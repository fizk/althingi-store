<?php

use Fizk\Router\Route;

use App\Handler;

return (new Route('root', '', []))
    ->addRoute(
        (new Route('loggjafarthing', '/loggjafarthing', ['handler' => Handler\Assemblies::class]))
            ->addRoute(
                (new Route('loggjafarthing.item', '/(?<assembly_id>\d+)', ['handler' => Handler\Assembly::class]))
                    ->addRoute((new Route('loggjafarthing.item.inflation', '/verdbolga', ['handler' => Handler\AssemblyInflation::class])))
                    ->addRoute((new Route('loggjafarthing.item.government', '/rikisstjorn', ['handler' => Handler\AssemblyGovernmentMinistries::class])))
                    ->addRoute((new Route('loggjafarthing.item.government', '/stjornarflokkar', ['handler' => Handler\AssemblyGovernmentParties::class])))
                    ->addRoute((new Route('loggjafarthing.item.parties', '/thingflokkar', ['handler' => Handler\AssemblyParties::class])))
                    ->addRoute((new Route('loggjafarthing.item.congressmen', '/thingmenn/(?<congressman_id>\d+)', ['handler' => Handler\AssemblyCongressman::class])))
                    ->addRoute(
                        (new Route('loggjafarthing.item.plenary', '/thingfundir/(?<plenary_id>\d+)', ['handler' => Handler\AssemblyPlenary::class]))
                            ->addRoute(new Route('loggjafarthing.item.plenary.agenda', '/lidir', ['handler' => Handler\AssemblyPlenaryAgenda::class]))
                            ->addRoute(new Route('loggjafarthing.item.plenary.agenda.item', '/lidir/(?<item_id>\d+)', ['handler' => Handler\AssemblyPlenaryAgendaItem::class]))
                    )
                    ->addRoute((new Route('loggjafarthing.item.plenaries', '/thingfundir', ['handler' => Handler\AssemblyPlenaries::class])))
                    ->addRoute(
                        (new Route('loggjafarthing.item.sessions', '/thingsetur', ['handler' => Handler\AssemblySittings::class]))
                            ->addRoute(new Route('loggjafarthing.item.sessions.committees', '/nefndir', ['handler' => Handler\AssemblyCommitteeSittings::class]))
                            ->addRoute(new Route('loggjafarthing.item.sessions.presidents', '/forsetar', ['handler' => Handler\AssemblyPresidentSittings::class]))
                            ->addRoute(new Route('loggjafarthing.item.sessions.parties', '/flokkar', ['handler' => Handler\AssemblyPartiesSittings::class]))
                            ->addRoute(new Route('loggjafarthing.item.sessions.constituencies', '/kjordaemi', ['handler' => Handler\AssemblyConstituenciesSittings::class]))
                    )
                )
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
        (new Route('radherraseta', '/radherraseta', ['handler' => handler\MinisterSittings::class]))
            ->addRoute(
                (new Route('radherraseta.item', '/(?<minister_sitting_id>\d+)', ['handler' => Handler\MinisterSitting::class]))
            )
    )
    ->addRoute(
        (new Route('thingmenn', '/thingmenn', ['handler' => handler\Congressmen::class]))
            ->addRoute(
                (new Route('thingmenn.item', '/(?<congressman_id>\d+)', ['handler' => Handler\Congressman::class]))
            )
    )
    ->addRoute(
        (new Route('forsetaseta', '/forsetaseta', ['handler' => handler\PresidentSittings::class]))
            ->addRoute(
                (new Route('forsetaseta.item', '/(?<president_id>\d+)', ['handler' => Handler\PresidentSitting::class]))
            )
    )
;
