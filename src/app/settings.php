<?php
declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => false,

            'database' => [
                'dns' => 'mysql:host=db;dbname=' . getenv('DB_NAME'),
                'user' => 'root',
                'password' => getenv('DB_PASSWORD'),
            ],
            'googleClientId' => getenv('GOOGLE_CLIENT_ID'),

            'doctrine' => [
                // if true, metadata caching is forcefully disabled
                'dev_mode' => true,

                // path where the compiled metadata info will be cached
                // make sure the path exists and it is writable
                'cache_dir' => APP_ROOT . '/../temp/cache/doctrine',

                // you should add any other path containing annotated entity classes
                'metadata_dirs' => [APP_ROOT . '/Model'],

                'connection' => [
                    'driver' => 'pdo_mysql',
                    'host' => 'db',
                    'port' => 3306,
                    'dbname' => 'pg',
                    'user' => 'root',
                    'password' => getenv('DB_PASSWORD'),
                    'charset' => 'utf8',
                ],
            ],
        ],
    ]);
};
