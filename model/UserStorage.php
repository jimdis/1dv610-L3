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

    private $credentials;
    private $user;

    public function __construct()
    {
        $this->loadUser();
    }

    public function login(\Model\Credentials $credentials): void
    {
        try {
            $this->credentials = $credentials;
            $this->validateLoginCredentials();
            $userDAL = new \Model\UserDAL();
            $user = $userDAL->getUser($this->credentials->getUsername());
            $isCorrectPassword = password_verify($this->credentials->getPassword(), $user->getPassword());
            if ($isCorrectPassword) {
                $user->setIsAuthenticated(true);
                $this->user = $user;
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
        $this->credentials = $credentials;
        $this->validateRegistrationCredentials();
        try {
            $hashedPassword = password_hash($this->credentials->getPassword(), PASSWORD_DEFAULT);
            $this->credentials->setPassword($hashedPassword);
            $userDAL = new \Model\UserDAL();
            $this->user = $userDAL->storeUser($this->credentials);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getUser(): \Model\User
    {
        return $this->user;
    }

    private function validateLoginCredentials(): void
    {
        if (strlen($this->credentials->getUsername()) == 0) throw new \Exception('Username is missing');
        if (strlen($this->credentials->getPassword()) == 0) throw new \Exception('Password is missing');
    }

    private function validateRegistrationCredentials(): void
    {
        $username = $this->credentials->getUsername();
        $password = $this->credentials->getPassword();
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
