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
