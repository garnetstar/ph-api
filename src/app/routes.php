<?php
declare(strict_types=1);

use Controllers\ArticleController;
use Controllers\LoginController;
use Middleware\Auth;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;


return function (App $app) {
	/**
	 * Article
	 */
	$app->group('', function (RouteCollectorProxyInterface $app) {
		$app->get('/article[/{id}]', ArticleController::class . ':get');
		$app->get('/article/filter/{field}/{word}', ArticleController::class . ':filter');
		$app->put('/article', ArticleController::class . ':put');
		$app->post('/article/{id}', ArticleController::class . ':post');
		$app->delete('/article/{id}', ArticleController::class . ':delete');
	})->add(Auth::class);

	/**
	 * Login
	 */
	$app->post('/login', LoginController::class . ':post');

};