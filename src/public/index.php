<?php

use DI\ContainerBuilder;
use Handlers\ErrorHandler;
use Monolog\Logger;
use Slim\Factory\AppFactory;

const APP_ROOT = __DIR__ . '/../app';

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

(require __DIR__ . '/../app/settings.php')($containerBuilder);

(require __DIR__ . '/../app/container.php')($containerBuilder);

$container = $containerBuilder->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->setBasePath('/api');
$app->addRoutingMiddleware();

$container = $app->getContainer();

$logger = $container->get(Logger::class);
$errorHandler = new ErrorHandler(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    $logger
);

$settings = $container->get('settings');

$errorMiddleware = $app->addErrorMiddleware($settings['displayErrorDetails'], true, true);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

(require __DIR__ . '/../app/routes.php')($app);

$app->run();
