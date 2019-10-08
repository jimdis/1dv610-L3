<?php

namespace Model;

session_start();
class UserStorage
{
    private static $salt = 'leflwefjweiofj823r';
    private static $SESSION_USERNAME = 'UserStorage::UserName';
    private static $SESSION_AGENT = 'HTTP_USER_AGENT';

    public static function loadUser(string $username, string $password): ?\Model\User
    {
        $username = $username;
        $password = self::createHash($password);
        //TODO: load user from DB instead of below.
        if ($username == 'Admin' && $password == self::createHash('Password')) {
            return new User($username, $password);
        } else return null;
    }

    public static function loadUserFromCookies(\Model\Cookies $cookies): ?\Model\User
    {
        $username = $cookies->getUsername();
        $password = $cookies->getPassword();
        //TODO: load user from DB instead of below.
        if ($username == 'Admin' && $password == self::createHash('Password')) {
            return new User($username, $password);
        } else return false;
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

    public static function saveSession(\Model\User $user)
    {
        $_SESSION[self::$SESSION_AGENT] = md5($_SERVER[self::$SESSION_AGENT]);
        $_SESSION[self::$SESSION_USERNAME] = $user->getUserName();
    }

    public static function destroySession()
    {
        session_destroy();
    }

    // public static function validateCookies(\Model\Cookies $cookies): bool
    // {
    //     //TODO: get user from storage as object based on cookie and compare passwords.

    //     public function validateCookies(string $username, string $password): bool
    // {
    //     $hashedPassword = $this->createHash($password);
    //     if ($username == $this->username && $hashedPassword == $this->password) {
    //         return true;
    //     } else return false;
    // }

    //     $user = new User('Admin', 'Password');
    //     return $cookies->validateCookies()
    // }

    public static function createHash(string $string): string
    {
        return md5(self::$salt . $string);
    }
}
