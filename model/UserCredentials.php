<?php

namespace Model;

class UserCredentials
{
    private $username;
    private $password;

    public function __construct(string $username, string $password)
    {
        if (strlen($username) == 0) throw new \Exception('Username is missing');
        if (strlen($password) == 0) throw new \Exception('Password is missing');

        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
