<?php

use Slim\App;

const APP_ROOT = __DIR__ . '/../app';

require __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../app/container.php';

$app = new App($container);

require __DIR__ . '/../app/routes.php';

$app->run();
