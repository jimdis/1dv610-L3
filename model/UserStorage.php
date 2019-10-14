<?php

namespace Model;

session_start();
class UserStorage
{
    private static $SESSION_USERNAME = 'UserStorage::UserName';
    private static $SESSION_AGENT = 'HTTP_USER_AGENT';

    public static function validateUserCredentials(\model\LoginCredentials $form): bool
    {
        //TODO: returnera en user?
        $user = new \Model\User();
        $id = $user->login($form->getUsername(), $form->getPassword());
        if ($id != null) {
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
