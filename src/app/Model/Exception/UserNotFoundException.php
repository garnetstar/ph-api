<?php
declare(strict_types=1);

namespace Model\Exception;

class UserNotFoundException extends \Exception
{

    public static function byLogin(string $login): self
    {
        return new static(sprintf('User with login = %s not found.', $login));
    }

    public static function byToken(string $token): self
    {
        return new static(sprintf('User with token = %s not found.', $token));
    }
}
