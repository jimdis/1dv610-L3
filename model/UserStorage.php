<?php

namespace Model;

require_once('exceptions/IncorrectCredentialsException.php');

session_start();
class UserStorage
//TODO: handle arg validation in separate methods?
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

    public function login(\Model\Credentials $credentials): void
    {
        try {
            $this->user = new User($credentials->getUsername(), $credentials->getPassword());
            $this->validateLoginCredentials();
            $userDAL = new \Model\UserDAL();
            $newUser = $userDAL->getUser($this->user->getUsername());
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
            $userDAL = new \Model\UserDAL();
            $user = $userDAL->getUserWithToken($credentials);
            $user->setIsAuthenticated(true);
            $this->user = $user;
            $this->saveSession();
        } catch (\Model\IncorrectCredentialsException $e) {
            throw new \Exception('Wrong information in cookies');
        }
    }

    public function storeToken(\Model\Token $token): void
    {
        $userDAL = new \Model\UserDAL();
        $userDAL->storeToken($token, $this->user->getUsername());
    }

    public function registerNewUser(\Model\Credentials $credentials): void
    {
        try {
            $this->validateRegistrationCredentials($credentials);
            $this->user = new \Model\User($credentials->getUsername(), $credentials->getPassword());
            $hashedPassword = password_hash($this->user->getPassword(), PASSWORD_DEFAULT);
            $this->user->setNewPassword($hashedPassword);
            $userDAL = new \Model\UserDAL();
            $userDAL->storeUser($this->user);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getUser(): \Model\User
    {
        return $this->user;
    }

    public function getUserIsAuthenticated(): bool
    {
        return $this->user->getIsAuthenticated();
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

    // public function sessionAuth(): void
    // {
    //     //TODO: verifiera password på något vis? Säkerhet?
    //     if (
    //         isset($_SESSION[self::$SESSION_AGENT]) &&
    //         $_SESSION[self::$SESSION_AGENT] == md5($_SERVER[self::$SESSION_AGENT]) &&
    //         isset($_SESSION[self::$SESSION_USERNAME])
    //     ) {
    //         $username = $_SESSION[self::$SESSION_USERNAME];
    //         $userDAL = new \Model\UserDAL();
    //         $user = $userDAL->getUser($username);
    //         $this->user = $user;
    //         $this->isAuthenticated = true;
    //     }
    // }

    private function loadUser(): void
    {
        if (
            isset($_SESSION[self::$SESSION_AGENT]) &&
            $_SESSION[self::$SESSION_AGENT] == md5($_SERVER[self::$SESSION_AGENT]) &&
            isset($_SESSION[self::$SESSION_USERNAME])
        ) {
            $username = $_SESSION[self::$SESSION_USERNAME];
            $userDAL = new \Model\UserDAL();
            $user = $userDAL->getUser($username);
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

    public static function validateCookies(string $username, string $password): bool
    {
        // TO BE IMPLEMENTED
        if ($username == 'Admin') {
            return true;
        } else return false;
    }
}
