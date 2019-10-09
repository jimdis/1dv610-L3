<?php

namespace Model;

// require_once("TooShortNameException.php");

class User
{
    private $username;
    private $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $this->applyFilter($username);
        $this->password = $password;
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setUsername(UserName $newName)
    {
        $this->username = $newName->getUserName();
    }

    public function hasUserName(): bool
    {
        return $this->username != null;
    }

    public static function applyFilter(string $rawInput): string
    {
        return trim(htmlentities($rawInput));
    }
}
