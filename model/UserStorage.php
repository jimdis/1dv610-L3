<?php

namespace Model;

session_start();
class UserStorage
{
    private static $SESSION_USERNAME = 'UserStorage::UserName';
    private static $SESSION_AGENT = 'HTTP_USER_AGENT';

    public static function validateUserCredentials(\model\UserCredentials $credentials): bool
    {
        if ($credentials->getUserName() == 'Admin' && $credentials->getPassword() == 'Password') {
            return true;
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

    public static function saveSession(\Model\UserCredentials $credentials)
    {
        $_SESSION[self::$SESSION_AGENT] = md5($_SERVER[self::$SESSION_AGENT]);
        $_SESSION[self::$SESSION_USERNAME] = $credentials->getUserName();
    }

    public static function destroySession()
    {
        session_destroy();
    }

    // private function loadUserFromCookies() : User {
    //     if (!isset($_COOKIE[$this->loginView->getCookieName()])) {
    //         return null;
    //     }
    //     if ($this->validateCookie($_COOKIE[$this->loginView->getCookieName()], $_COOKIE[$this->loginView->getCookiePassword()])) {
    //         $this->isLoggedIn = true;
    //         $this->saveSession();
    //         $this->message = 'Welcome back with cookie';
    //     }
    // }
}
