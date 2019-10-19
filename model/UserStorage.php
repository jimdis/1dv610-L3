<?php

namespace Model;

require_once('Exceptions/IncorrectCredentialsException.php');

session_start();
class UserStorage
{
    private static $minNameLength = 3;
    private static $minPasswordLength = 6;
    private static $SESSION_USERNAME = 'UserStorage::UserName';
    private static $SESSION_AGENT = 'HTTP_USER_AGENT';

    private $user;

    public function __construct()
    {
        $this->loadUser();
    }


    public function getUsername(): string
    {
        return $this->user->getUsername();
    }

    public function getUserIsAuthenticated(): bool
    {
        return $this->user->getIsAuthenticated();
    }

    public function login(\Model\Credentials $credentials): void
    {
        try {
            $this->user = new User($credentials->getUsername(), $credentials->getPassword());
            $this->validateLoginCredentials();
            $newUser = \Model\UserDAL::getUser($this->user->getUsername());
            $isCorrectPassword = password_verify($this->user->getPassword(), $newUser->getPassword());
            if ($isCorrectPassword) {
                $newUser->setIsAuthenticated(true);
                $this->user = $newUser;
                $this->saveSession();
            } else throw new \Model\IncorrectCredentialsException();
        } catch (\Model\IncorrectCredentialsException $e) {
            throw new \Exception('Wrong name or password');
        }
    }

    public function loginWithToken(\model\Credentials $credentials): void
    {
        try {
            $user = \Model\UserDAL::getUserWithToken($credentials);
            $user->setIsAuthenticated(true);
            $this->user = $user;
            $this->saveSession();
        } catch (\Model\IncorrectCredentialsException $e) {
            throw new \Exception('Wrong information in cookies');
        }
    }

    public function storeToken(\Model\Token $token): void
    {
        \Model\UserDAL::storeToken($token, $this->user->getUsername());
    }

    public function registerNewUser(\Model\Credentials $credentials): void
    {
        try {
            $this->validateRegistrationCredentials($credentials);
            $this->user = new \Model\User($credentials->getUsername(), $credentials->getPassword());
            $hashedPassword = password_hash($this->user->getPassword(), PASSWORD_DEFAULT);
            $this->user->setNewPassword($hashedPassword);
            \Model\UserDAL::storeUser($this->user);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function validateLoginCredentials(): void
    {
        if (strlen($this->user->getUsername()) == 0) throw new \Exception('Username is missing');
        if (strlen($this->user->getPassword()) == 0) throw new \Exception('Password is missing');
    }

    private function validateRegistrationCredentials(\Model\Credentials $credentials): void
    {
        $username = $credentials->getUsername();
        $password = $credentials->getPassword();
        $errorMessage = '';
        if (strlen($username) < self::$minNameLength) {
            $errorMessage .= 'Username has too few characters, at least 3 characters.';
        }

        if (strlen($password) < self::$minPasswordLength) {
            $message = 'Password has too few characters, at least 6 characters.';
            $errorMessage .= strlen($errorMessage) > 0 ?  '<br />' . $message : $message;
        }

        if ($username != htmlspecialchars($username)) {
            $errorMessage .= 'Username contains invalid characters.';
        }

        if (strlen($errorMessage) > 0) {
            throw new \Exception($errorMessage);
        }
    }

    private function loadUser(): void
    {
        if (
            isset($_SESSION[self::$SESSION_AGENT]) &&
            $_SESSION[self::$SESSION_AGENT] == md5($_SERVER[self::$SESSION_AGENT]) &&
            isset($_SESSION[self::$SESSION_USERNAME])
        ) {
            $username = $_SESSION[self::$SESSION_USERNAME];
            $user = \Model\UserDAL::getUser($username);
            $user->setIsAuthenticated(true);
            $this->user = $user;
        } else {
            $this->user = new \Model\User('', '');
        }
    }

    private function saveSession()
    {
        $_SESSION[self::$SESSION_AGENT] = md5($_SERVER[self::$SESSION_AGENT]);
        $_SESSION[self::$SESSION_USERNAME] = $this->user->getUsername();
    }

    public function logout()
    {
        session_destroy();
        $this->user = new \Model\User('', '');
    }
}
