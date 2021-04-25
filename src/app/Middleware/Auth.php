<?php

declare(strict_types=1);

namespace Middleware;

use Doctrine\ORM\EntityManager;
use Exception;
use Model\Exception\UserNotFoundException;
use Model\User\User;
use Model\User\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Auth
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        private EntityManager $entityManager,
    ) {
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function __invoke(
        Request $request,
        MiddlewareDispatcherInterface $handler
    ): ResponseInterface {

        $token = null;
        $headers = $request->getHeaders();
        $auth = $headers['Authorization'][0] ?? '';
        $authParts = explode(' ', $auth);

        if (count($authParts) === 2 && $authParts[0] === 'Bearer') {
            $token = $authParts[1];
        }

        if ($this->isTokenValid($token)) {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write('Permission denied');
            $response = $response->withStatus(403);
        }

        return $response;
    }

    private function isTokenValid(?string $token): bool
    {
        if ($token === null) {
            return false;
        }

        try {
            $user = $this->userRepository->getByToken($token);
        } catch (UserNotFoundException $e) {
            return false;
        }

        return true;
    }
}
