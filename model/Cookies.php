<?php

namespace Model;

class Cookies
{
    private static $EXPIRY_IN_DAYS = 30;
    private $username;
    private $password;
    private $expires;


    public function __construct(string $username, string $password = null)
    {
        $this->username = $username;
        $this->password = $password ?? bin2hex(random_bytes(16));
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
