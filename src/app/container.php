<?php
declare(strict_types=1);

use Command\MigrateCommand;
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
use Model\UserRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nette\Database\Connection;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions(
        [
            'isProduction' => true,
            'database' => static function (ContainerInterface $container) {
                $settings = $container->get('settings');
                $database = new Connection(
                    $settings['database']['dns'],
                    $settings['database']['user'],
                    $settings['database']['password']
                );

                return $database;
            },

            ArticleController::class => function (ContainerInterface $container) {
                return new ArticleController(
                    $container->get(EntityManager::class),
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

            UserRepository::class => function (
                ContainerInterface $container
            ) {
                return new UserRepository(
                    $container->get('database')
                );
            },

            Auth::class => function (Container $container) {
                $userRepository = $container->get(UserRepository::class);

                return new Auth($userRepository);
            },

            MigrateCommand::class => function (Container $container) {
                return new MigrateCommand($container->get(EntityManager::class), $container->get('database'));
            },

			\Command\AlgoliaBuildCommand::class => function (Container $container) {
        		return new \Command\AlgoliaBuildCommand($container->get(EntityManager::class));
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
        ]
    );
};
