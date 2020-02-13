<?php
declare(strict_types=1);

namespace Model\User;

use Doctrine\ORM\EntityRepository;
use Model\Exception\UserNotFoundException;

class UserRepository extends EntityRepository
{

    /**
     * @param string $login
     * @return User
     * @throws UserNotFoundException
     */
    public function getByLogin(string $login): User
    {
        /** @var User $user */
        $user = $this->findOneBy(['login' => $login]);
        if ($user === null) {
            throw UserNotFoundException::byLogin($login);
        }

        return $user;
    }

    /**
     * @param string $token
     * @return User
     * @throws UserNotFoundException
     */
    public function getByToken(string $token): User
    {
        /** @var User $user */
        $user = $this->findOneBy(['token' => $token]);
        if ($user === null) {
            throw UserNotFoundException::byToken($token);
        }

        return $user;
    }
}
