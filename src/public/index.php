<?php

use Slim\App;

//phpinfo();

require __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../app/container.php';

$app = new App($container);

require __DIR__ . '/../app/routes.php';

require __DIR__ . '/../app/functions.php';

$app->run();
