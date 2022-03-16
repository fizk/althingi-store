<?php

use App\Handler;
use App\Service;
use Psr\Container\ContainerInterface;
use MongoDB\Database;
use MongoDB\Client;

return [
    'factories' => [
        Handler\Assemblies::class => function (ContainerInterface $container) {
            return (new Handler\Assemblies())
                ->setAssemblyService($container->get(Service\Assembly::class))
            ;
        },
        Handler\Assembly::class => function (ContainerInterface $container) {
            return (new Handler\Assembly())
                ->setAssemblyService($container->get(Service\Assembly::class))
                ->setMinistryService($container->get(Service\Ministry::class))
                ->setCommitteeService($container->get(Service\Committee::class))
                ->setCongressmanSittingService($container->get(Service\CongressmanSitting::class))
                ->setCommitteeSittingService($container->get(Service\CommitteeSitting::class))
            ;
        },
        Handler\Ministries::class => function (ContainerInterface $container) {
            return (new Handler\Ministries())
                ->setMinistryService($container->get(Service\Ministry::class))
            ;
        },
        Handler\Ministry::class => function (ContainerInterface $container) {
            return (new Handler\Ministry())
                ->setMinistryService($container->get(Service\Ministry::class))
            ;
        },
        Handler\Parties::class => function (ContainerInterface $container) {
            return (new Handler\Parties())
                ->setPartyService($container->get(Service\Party::class))
            ;
        },
        Handler\Party::class => function (ContainerInterface $container) {
            return (new Handler\Party())
                ->setPartyService($container->get(Service\Party::class))
                ->setCongressmanSittingService($container->get(Service\CongressmanSitting::class))
                ->setPartyService($container->get(Service\Party::class))
            ;
        },
        Handler\Committees::class => function (ContainerInterface $container) {
            return (new Handler\Committees())
                ->setCommitteeService($container->get(Service\Committee::class))
            ;
        },
        Handler\Committee::class => function (ContainerInterface $container) {
            return (new Handler\Committee())
                ->setCommitteeService($container->get(Service\Committee::class))
            ;
        },
        Handler\Constituencies::class => function (ContainerInterface $container) {
            return (new Handler\Constituencies())
                ->setConstituencyService($container->get(Service\Constituency::class))
            ;
        },
        Handler\Constituency::class => function (ContainerInterface $container) {
            return (new Handler\Constituency())
                ->setConstituencyService($container->get(Service\Constituency::class))
                ->setCongressmanSittingService($container->get(Service\CongressmanSitting::class))
                ->setCommitteeSittingService($container->get(Service\CommitteeSitting::class))
            ;
        },
        Handler\Inflations::class => function (ContainerInterface $container) {
            return (new Handler\Inflations())
                ->setInflationService($container->get(Service\Inflation::class))
            ;
        },
        Handler\Inflation::class => function (ContainerInterface $container) {
            return (new Handler\Inflation())
                ->setInflationService($container->get(Service\Inflation::class))
            ;
        },
        Handler\CongressmanSitting::class => function (ContainerInterface $container) {
            return (new Handler\CongressmanSitting())
                ->setCongressmanSittingService($container->get(Service\CongressmanSitting::class))
            ;
        },
        Handler\CongressmanSittings::class => function (ContainerInterface $container) {
            return (new Handler\CongressmanSittings())
                ->setCongressmanSittingService($container->get(Service\CongressmanSitting::class))
            ;
        },
        Handler\CommitteeSitting::class => function (ContainerInterface $container) {
            return (new Handler\CommitteeSitting())
            ->setCommitteeSittingService($container->get(Service\CommitteeSitting::class))
            ;
        },
        Handler\CommitteeSittings::class => function (ContainerInterface $container) {
            return (new Handler\CommitteeSittings())
            ->setCommitteeSittingService($container->get(Service\CommitteeSitting::class))
            ;
        },




        Service\Assembly::class => function (ContainerInterface $container) {
            return (new Service\Assembly)
                ->setSourceDatabase($container->get(Database::class));
        },
        Service\Ministry::class => function (ContainerInterface $container) {
            return (new Service\Ministry)
                ->setSourceDatabase($container->get(Database::class));
        },
        Service\Party::class => function (ContainerInterface $container) {
            return (new Service\Party)
                ->setSourceDatabase($container->get(Database::class));
        },
        Service\Committee::class => function (ContainerInterface $container) {
            return (new Service\Committee)
                ->setSourceDatabase($container->get(Database::class));
        },
        Service\Constituency::class => function (ContainerInterface $container) {
            return (new Service\Constituency)
                ->setSourceDatabase($container->get(Database::class));
        },
        Service\Inflation::class => function (ContainerInterface $container) {
            return (new Service\Inflation)
                ->setSourceDatabase($container->get(Database::class));
        },
        Service\CongressmanSitting::class => function (ContainerInterface $container) {
            return (new Service\CongressmanSitting)
                ->setSourceDatabase($container->get(Database::class));
        },
        Service\CommitteeSitting::class => function (ContainerInterface $container) {
            return (new Service\CommitteeSitting)
                ->setSourceDatabase($container->get(Database::class));
        },


        Database::class => function (ContainerInterface $container) {
            $dbHost = getenv('STORE_DB_HOST') ?? '127.0.0.1';
            $dbUser = getenv('STORE_DB_USER') ?? 'root';
            $dbPassword = getenv('STORE_DB_PASSWORD') ?? 'password';

            $client = new Client("mongodb://{$dbHost}", [
                'username' => $dbUser,
                'password' => $dbPassword,
            ], []);

            return $client->selectDatabase('althingi');
        }
    ],
];
