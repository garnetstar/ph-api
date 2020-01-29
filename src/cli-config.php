<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

const APP_ROOT = __DIR__ . '/app';

$containerBuilder = new ContainerBuilder();

(require __DIR__ . '/app/settings.php')($containerBuilder);
(require __DIR__ . '/app/container.php')($containerBuilder);

$container = $containerBuilder->build();

ConsoleRunner::run(
	ConsoleRunner::createHelperSet($container->get(EntityManager::class))
);