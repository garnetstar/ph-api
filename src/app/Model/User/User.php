<?php
declare(strict_types=1);

namespace Model\User;

use Doctrine\ORM\Mapping as ORM;
use Model\DateTime\DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Model\User\UserRepository")
 */
class User
{

	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	private $id;

	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=false)
	 */
	private $login;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $token;

	public function __construct(string $login, string $token)
	{
		$this->login = $login;
		$this->token = $token;
	}

	public function getLogin(): string
	{
		return $this->login;
	}

	public function getToken(): string
	{
		return $this->token;
	}
}

