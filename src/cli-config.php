<?php
declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Slim\Container;

const APP_ROOT = __DIR__ . '/app';

/** @var Container $container */
$container = require __DIR__ . '/app/container.php';

ConsoleRunner::run(
	ConsoleRunner::createHelperSet($container[EntityManager::class])
);