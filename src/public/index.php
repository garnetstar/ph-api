<?php

use DI\ContainerBuilder;
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

(require __DIR__ . '/../app/routes.php')($app);

$app->addErrorMiddleware(true, true, true);

$app->run();
