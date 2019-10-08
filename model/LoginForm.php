<?php

namespace Model;

class LoginForm
{
    private $action;
    private $username;
    private $password;

    public function __construct(string $action, string $username, string $password)
    {
        $this->action = $action;
        if ($this->action == \Model\FormAction::$login) {
            $this->username = $username;
            $this->password = $password;
            $this->validateLoginData();
        }
    }

    private function validateLoginData(): void
    {
        if (strlen($this->username) == 0) throw new \Exception('Username is missing');
        if (strlen($this->password) == 0) throw new \Exception('Password is missing');
    }

    public function getAction(): string
    {
        return $this->action;
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
