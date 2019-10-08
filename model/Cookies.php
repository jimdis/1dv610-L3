<?php

namespace Model;

class Cookies
{
    private static $EXPIRY_IN_DAYS = 30;
    private $username;
    private $password;
    private $expires;


    public function __construct(\Model\User $user)
    {

        $this->username = $user->getUserName();
        $this->password = \Model\UserStorage::createHash($user->getPassword());
        $this->expires = time() + 60 * 60 * 24 * self::$EXPIRY_IN_DAYS;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getExpires(): int
    {
        return $this->expires;
    }
}
