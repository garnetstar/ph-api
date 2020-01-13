<?php
declare(strict_types=1);

use Command\MigrateCommand;
use Controllers\ArticleController;
use Controllers\GymController;
use Controllers\LoginController;
use Controllers\TagController;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Middleware\Auth;
use Model\UserRepository;
use Nette\Database\Connection;
use Nette\Database\Context;
use Nette\Database\Structure;
use Slim\Container;

$settings = require __DIR__ . '/../app/settings.php';

/** @var Container $container */
$container = new Container(['settings' => $settings]);

$container['database'] = function (Container $container) {
	$settings = $container['settings']['database'];
	$database = new Connection(
		$settings['dns'],
		$settings['user'],
		$settings['password']
	);
	return $database;
};

//cache
$container['storage'] = function ($c) {
	return new Nette\Caching\Storages\FileStorage('temp');
};

//database context
$container['database-context'] = function ($c) {
	$storage = new Nette\Caching\Storages\FileStorage(APP_ROOT . '/../temp');
//	$databaseCache = new \Nette\Caching\Cache($storage);
	$structure = new Structure($c->get('database'), $storage);
	return new Context($c->database, $structure);
};

$container[ArticleController::class] = function (Container $container) {
	return new ArticleController(
		$container->get(EntityManager::class)
	);
};

$container[LoginController::class] = function (Container $container) {
	$googleClientId = $container['settings']['googleClientId'];
	return new LoginController(
		$googleClientId,
		$container[EntityManager::class]
	);
};

$container[UserRepository::class] = function (Container $container) {
	return new UserRepository($container['database']);
};

$container[Auth::class] = function (Container $container) {
	$userRepository = $container[UserRepository::class];
	return new Auth($userRepository);
};

$container[EntityManager::class] = function (Container $container) {
	$config = Setup::createAnnotationMetadataConfiguration(
		$container['settings']['doctrine']['metadata_dirs'],
		$container['settings']['doctrine']['dev_mode']
	);

	$config->setMetadataDriverImpl(
		new AnnotationDriver(
			new AnnotationReader(),
			$container['settings']['doctrine']['metadata_dirs']
		)
	);

	$config->setMetadataCacheImpl(
		new FilesystemCache(
			$container['settings']['doctrine']['cache_dir']
		)
	);

	return EntityManager::create(
		$container['settings']['doctrine']['connection'],
		$config
	);
};

$container[MigrateCommand::class] = function (Container $container) {
	return new MigrateCommand($container[EntityManager::class], $container['database']);
};

return $container;