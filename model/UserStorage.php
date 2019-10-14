<?php

namespace Model;

session_start();
class UserStorage
//TODO: handle arg validation in separate methods?
{
    private static $minNameLength = 3;
    private static $minPasswordLength = 6;
    private static $SESSION_USERNAME = 'UserStorage::UserName';
    private static $SESSION_AGENT = 'HTTP_USER_AGENT';

    public static function loginUser(string $username, string $password): \Model\User
    {
        try {
            if (strlen($username) == 0) throw new \Exception('Username is missing');
            if (strlen($password) == 0) throw new \Exception('Password is missing');
            $userDAL = new \Model\UserDAL();
            $user = $userDAL->getUser($username);
            $isCorrectPassword = password_verify($password, $user->getPassword());
            if ($isCorrectPassword) {
                return $user;
            } else throw new \Exception();
        } catch (\Exception $e) {
            throw new \Exception('Wrong name or password');
        }
    }

    public static function registerNewUser(string $username, string $password)
    {
        self::validateRegistrationCredentials($username, $password);
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userDAL = new \Model\UserDAL();
            $userDAL->register($username, $hashedPassword);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private static function validateRegistrationCredentials(string $username, string $password): void
    {
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

    // fixa så nedan returnerar en User-instans. Gör abstrakt, ta ner isset etc till en egen metod.
    // public function loadUser() : User
    // {
    //     $sessionUser = $this->loadUserFromSession();
    //     $cookieUser = $this->loadUserFromCookies();
    //     if ($sessionUser) return $sessionUser;
    // }

    // gör eventuellt om till att returnera en user, alt username..
    public static function validateSession(): bool
    {
        if (
            isset($_SESSION[self::$SESSION_AGENT]) &&
            $_SESSION[self::$SESSION_AGENT] == md5($_SERVER[self::$SESSION_AGENT]) &&
            isset($_SESSION[self::$SESSION_USERNAME])
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function saveSession(string $username)
    {
        $_SESSION[self::$SESSION_AGENT] = md5($_SERVER[self::$SESSION_AGENT]);
        $_SESSION[self::$SESSION_USERNAME] = $username;
    }

    public static function destroySession()
    {
        session_destroy();
    }

    public static function validateCookies(string $username, string $password): bool
    {
        // TO BE IMPLEMENTED
        if ($username == 'Admin') {
            return true;
        } else return false;
    }
}
