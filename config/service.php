<?php

use App\Event\{ErrorEvent, SystemSuccessEvent};
use App\Handler;
use App\Service;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use MongoDB\Database;
use MongoDB\Client;
use League\Event\PrioritizedListenerRegistry;
use League\Event\EventDispatcher;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

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
                ->setMinisterSittingService($container->get(Service\MinisterSitting::class))
            ;
        },
        Handler\AssemblyParties::class => function (ContainerInterface $container) {
            return (new Handler\AssemblyParties())
                ->setCongressmanSittingService($container->get(Service\CongressmanSitting::class))
            ;
        },
        Handler\AssemblyGovernmentParties::class => function (ContainerInterface $container) {
            return (new Handler\AssemblyGovernmentParties())
                ->setMinisterSittingService($container->get(Service\MinisterSitting::class))
            ;
        },
        Handler\AssemblyGovernmentMinistries::class => function (ContainerInterface $container) {
            return (new Handler\AssemblyGovernmentMinistries())
                ->setMinisterSittingService($container->get(Service\MinisterSitting::class))
            ;
        },
        Handler\AssemblySittings::class => function (ContainerInterface $container) {
            return (new Handler\AssemblySittings())
                ->setCongressmanSittingService($container->get(Service\CongressmanSitting::class))
            ;
        },
        Handler\AssemblyPartiesSittings::class => function (ContainerInterface $container) {
            return (new Handler\AssemblyPartiesSittings())
                ->setCongressmanSittingService($container->get(Service\CongressmanSitting::class))
            ;
        },
        Handler\AssemblyConstituenciesSittings::class => function (ContainerInterface $container) {
            return (new Handler\AssemblyConstituenciesSittings())
                ->setCongressmanSittingService($container->get(Service\CongressmanSitting::class))
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
                ->setMinisterSittingService($container->get(Service\MinisterSitting::class))
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
                ->setMinisterSittingService($container->get(Service\MinisterSitting::class))
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
                ->setMinisterSittingService($container->get(Service\MinisterSitting::class))
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
        Handler\MinisterSittings::class => function (ContainerInterface $container) {
            return (new Handler\MinisterSittings())
                ->setMinisterSittingService($container->get(Service\MinisterSitting::class))
            ;
        },
        Handler\MinisterSitting::class => function (ContainerInterface $container) {
            return (new Handler\MinisterSitting())
                ->setMinisterSittingService($container->get(Service\MinisterSitting::class))
            ;
        },
        Handler\CommitteeSittings::class => function (ContainerInterface $container) {
            return (new Handler\CommitteeSittings())
                ->setCommitteeSittingService($container->get(Service\CommitteeSitting::class))
            ;
        },
        Handler\Congressman::class => function (ContainerInterface $container) {
            return (new Handler\Congressman())
                ->setCongressmanService($container->get(Service\Congressman::class))
                ->setCommitteeSittingService($container->get(Service\CommitteeSitting::class))
                ->setCongressmanSittingService($container->get(Service\CongressmanSitting::class))
                ->setMinisterSittingService($container->get(Service\MinisterSitting::class))
            ;
        },
        Handler\Congressmen::class => function (ContainerInterface $container) {
            return (new Handler\Congressmen())
                ->setCongressmanService($container->get(Service\Congressman::class))
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
        Service\Congressman::class => function (ContainerInterface $container) {
            return (new Service\Congressman)
                ->setSourceDatabase($container->get(Database::class));
        },
        Service\MinisterSitting::class => function (ContainerInterface $container) {
            return (new Service\MinisterSitting)
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
        },
        LoggerInterface::class => function (ContainerInterface $container, $requestedName) {
            return (new Logger('aggregator'))
            ->pushHandler((new StreamHandler('php://stdout', Logger::DEBUG))
                    ->setFormatter(new LineFormatter("[%datetime%] %level_name% %message%\n"))
            );
        },
        EventDispatcherInterface::class => function (ContainerInterface $container, $requestedName) {
            $logger = $container->get(LoggerInterface::class);
            $provider = new PrioritizedListenerRegistry();

            $provider->subscribeTo(ErrorEvent::class, function (ErrorEvent $event) use ($logger) {
                $logger->error((string) $event);
            });
            $provider->subscribeTo(SystemSuccessEvent::class, function (SystemSuccessEvent $event) use ($logger) {
                $logger->debug((string) $event);
            });

            return new EventDispatcher($provider);
        },
    ],
];
