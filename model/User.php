<?php

namespace Model;

class User
{
    private $username;
    private $password;
    private $isAuthenticated = false;

    public function __construct(string $username, string $password)
    {
        $this->username = \Model\SanitizeInput::sanitize($username);
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

    public function getIsAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    public function setIsAuthenticated(bool $bool)
    {
        $this->isAuthenticated = $bool;
    }

    public function setNewPassword(string $newPassword)
    {
        $this->password = $newPassword;
    }
}
