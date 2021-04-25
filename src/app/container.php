<?php

declare(strict_types=1);

use Algolia\AlgoliaSearch\SearchClient;
use Command\AlgoliaBuildCommand;
use Controllers\ArticleController;
use Controllers\LoginController;
use DI\Container;
use DI\ContainerBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Middleware\Auth;
use Model\Search\AlgoliaIndexer;
use Model\Search\AlgoliaSearchManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions(
        [
            'isProduction' => true,

            ArticleController::class => function (ContainerInterface $container) {
                return new ArticleController(
                    $container->get(EntityManager::class),
                    $container->get(AlgoliaSearchManager::class),
                    $container->get(Logger::class)
                );
            },

            LoginController::class => function (ContainerInterface $container) {
                $settings = $container->get('settings');

                return new LoginController(
                    $settings['googleClientId'],
                    $container->get(EntityManager::class)
                );
            },

            Auth::class => function (Container $container) {
                return new Auth($container->get(EntityManager::class));
            },

            AlgoliaBuildCommand::class => function (Container $container) {
                return new AlgoliaBuildCommand(
                    $container->get(AlgoliaIndexer::class)
                );
            },

            EntityManager::class => function (Container $container) {
                $settings = $container->get('settings');

                $config = Setup::createAnnotationMetadataConfiguration(
                    $settings['doctrine']['metadata_dirs'],
                    $settings['doctrine']['dev_mode'],
                    $settings['doctrine']['cache_dir']
                );

                $config->setMetadataDriverImpl(
                    new AnnotationDriver(
                        new AnnotationReader(),
                        $settings['doctrine']['metadata_dirs']
                    )
                );

                $config->setMetadataCacheImpl(
                    new FilesystemCache(
                        $settings['doctrine']['cache_dir']
                    )
                );

                return EntityManager::create(
                    $settings['doctrine']['connection'],
                    $config
                );
            },

            Logger::class => function (ContainerInterface $container) {
                $streamHandler = new StreamHandler(APP_ROOT . '/../log/app.log', Logger::DEBUG);
                $logger = new Logger('pg-api');
                $logger->pushHandler($streamHandler);

                return $logger;
            },

            SearchClient::class => static function (ContainerInterface $container) {
                $settings = $container->get('settings');
                $client = SearchClient::create(
                    $settings['algolia']['appId'],
                    $settings['algolia']['apiKey']
                );

                return $client;
            },

            AlgoliaIndexer::class => function (ContainerInterface $container) {
                return new AlgoliaIndexer(
                    $container->get(SearchClient::class),
                    $container->get(EntityManager::class)
                );
            },

            AlgoliaSearchManager::class => function (ContainerInterface $container) {
                return new AlgoliaSearchManager($container->get(SearchClient::class));
            },
        ]
    );
};
