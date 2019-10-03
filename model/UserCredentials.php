<?php

namespace Model;

class UserCredentials
{
    private $name;
    private $password;

    public function __construct(string $name, string $password)
    {
        if (strlen($name) == 0) throw new \Exception('Username is missing');
        if (strlen($password) == 0) throw new \Exception('Password is missing');

        $this->name = $name;
        $this->password = $password;
    }


    public function getUserName()
    {
        return $this->name;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
