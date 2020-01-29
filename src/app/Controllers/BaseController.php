<?php
declare(strict_types=1);

namespace Controllers;

use Slim\Psr7\Response;

class BaseController
{

	protected function returnJson(Response $response): Response
	{
		return $response->withHeader('Content-Type', 'application/json');
	}
}