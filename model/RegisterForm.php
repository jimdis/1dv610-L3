<?php

namespace Model;

class RegisterForm
{
    private static $minNameLength = 3;
    private static $minPasswordLength = 6;
    private $action;
    private $username;
    private $password;
    private $passwordRepeat;

    public function __construct(string $action, string $username, string $password, string $passwordRepeat)
    {
        $this->action = $action;
        if ($this->action == \Model\FormAction::$register) {
            $this->username = $username;
            $this->password = $password;
            $this->passwordRepeat = $passwordRepeat;
            $this->validateRegisterData();
        }
    }

    private function validateRegisterData(): void
    {
        $errorMessage = '';
        if (strlen($this->username) < self::$minNameLength) {
            $errorMessage .= 'Username has too few characters, at least 3 characters.';
        }

        if (strlen($this->password) < self::$minPasswordLength) {
            $message = 'Password has too few characters, at least 6 characters.';
            $errorMessage .= strlen($errorMessage) > 0 ?  '<br />' . $message : $message;
        }

        if ($this->password != $this->passwordRepeat) {
            $errorMessage .= 'Passwords do not match.';
        }

        if ($this->username != htmlspecialchars($this->username)) {
            $errorMessage .= 'Username contains invalid characters.';
        }

        if (strlen($errorMessage) > 0) {
            throw new \Exception($errorMessage);
        }
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
