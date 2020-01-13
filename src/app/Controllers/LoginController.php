<?php
declare(strict_types=1);

namespace Controllers;

use Doctrine\ORM\EntityManager;
use Model\Exception\UserNotFoundException;
use Model\User\User;
use Model\User\UserRepository;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginController
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
		$data = json_decode($request->getBody()->getContents());
		if (isset($data->id_token)) {

			$client = new \Google_Client(['client_id' => $this->clientId]);
			$payload = $client->verifyIdToken($data->id_token);

			if ($user = $this->getUser($payload)) {
				$response = $response->withJson(
					[
						'token' => $user->getToken(),
					]
				);
			} else {
				$response = $response->withStatus(401)->write('Invalid credentials');
			}
		} else {
			$response = $response->withStatus(401)
				->write('Missing parameter');
		}

		return $response;
	}

	private function getUser($payload): User
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
