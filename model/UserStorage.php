<?php

namespace Model;

session_start();
class UserStorage
{
    private static $SESSION_USERNAME =  __CLASS__ .  "::UserName";
    private static $SESSION_AGENT =  __CLASS__ .  "::UserAgent";

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

    // private function loadUserFromSession() : User {
    //     if (
    //         isset($_SESSION[self::$SESSION_AGENT]) &&
    //         $_SESSION[self::$SESSION_AGENT] == md5($_SERVER[self::$SESSION_AGENT]) &&
    //         isset($_SESSION[self::$SESSION_USERNAME])
    //     ) {
    //         return $_SESSION[self::$SESSION_USERNAME];
    //     } else {
    //         return null; // fixa: kolla cookies etc.
    //     }
    // }

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

    // public function saveUser(UserName $toBeSaved)
    // {
    //     $_SESSION[self::$SESSION_AGENT] = md5($_SERVER[self::$SESSION_AGENT]);
    //     $_SESSION[self::$SESSION_USERNAME] = $toBeSaved;
    // }
}
