<?php
declare(strict_types=1);

namespace Controllers;

use Doctrine\ORM\EntityManager;
use Google_Client;
use Model\Exception\UserNotFoundException;
use Model\User\User;
use Model\User\UserRepository;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class LoginController extends BaseController
{

	/**
	 * @var string
	 */
	private $clientId;

	/**
	 * @var UserRepository
	 */
	private $userRepository;

	public function __construct(string $googleClientId, EntityManager $entityManager)
	{
		$this->clientId = $googleClientId;
		$this->userRepository = $entityManager->getRepository(User::class);
	}

	public function post(Request $request, Response $response, array $args)
	{
		$data = json_decode($request->getBody()->getContents(), true);
		if (isset($data['id_token'])) {

			$client = new Google_Client(['client_id' => $this->clientId]);
			$payload = $client->verifyIdToken($data['id_token']);

			if ($user = $this->getUser($payload)) {
				$body = [
					'token' => $user->getToken(),
				];

				$response->getBody()->write(json_encode($body));

				return $this->returnJson($response);
			}

			$response->getBody()->write('Invalid credentials');

			return $response->withStatus(401);
		}

		$response->getBody()->write('Missing parameter');

		return $response->withStatus(422);
	}

	private function getUser($payload): ?User
	{
		if ($payload === false) {
			return null;
		}

		try {
			return $this->userRepository->getByLogin($payload['email']);
		} catch (UserNotFoundException $e) {
			return null;
		}
	}
}
