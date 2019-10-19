<?php

namespace View;

class LoginView extends View
{
    private static $login = 'LoginView::Login';
    private static $logout = 'LoginView::Logout';
    private static $name = 'LoginView::UserName';
    private static $password = 'LoginView::Password';
    private static $cookieName = 'LoginView::CookieName';
    private static $cookiePassword = 'LoginView::CookiePassword';
    private static $keep = 'LoginView::KeepMeLoggedIn';
    private static $messageId = 'LoginView::Message';

    public function loginFormWasSubmitted(): bool
    {
        return isset($_POST[self::$login]);
    }

    public function getCredentials(): \Model\Credentials
    {
        if ($this->userHasCookies()) {
            $username = $_COOKIE[self::$cookieName];
            $password = $_COOKIE[self::$cookiePassword];
        } else {
            $username = $_POST[self::$name] ?? '';
            $password = $_POST[self::$password] ?? '';
        }
        return new \Model\Credentials($username, $password);
    }

    public function logoutWasSubmitted(): bool
    {
        return isset($_POST[self::$logout]);
    }

    public function keepLoggedIn(): bool
    {
        return isset($_POST[self::$keep]);
    }

    public function setCookies(): \Model\Token
    {
        $token = new \Model\Token();
        $expiry = $token->getExpires();
        $cookieUsername = $this->getCredentials()->getUsername();
        $cookiePassword = $token->getContent();
        setcookie(self::$cookieName, $cookieUsername, $expiry);
        setcookie(self::$cookiePassword, $cookiePassword, $expiry);
        return $token;
    }

    public function userHasCookies(): bool
    {
        return isset($_COOKIE[self::$cookieName]) && isset($_COOKIE[self::$cookiePassword]);
    }

    public function unsetCookies(): void
    {
        if ($this->userHasCookies()) {
            setcookie(self::$cookieName, '', time() - 3600);
            setcookie(self::$cookiePassword, '', time() - 3600);
        }
    }

    public function show(): string
    {
        $html =  $this->storage->getUserIsAuthenticated()
            ? $this->generateLoggedInHTML()
            : $this->generateLoginFormHTML();
        return $html;
    }

    private function generateLoggedInHTML(): string
    {
        $form = '<a href="?messages">Go to message board</a><br />
                <form  method="post" >
                    <p id="' . self::$messageId . '">' . $this->message . '</p>
                    <input type="submit" name="' . self::$logout . '" value="logout"/>
                </form>';
        $html = $form . '
                <br/>
                <h2>Your messages</h2>
                ' . \View\MessageTable::showUserMessages($this->storage->getUsername());
        return $html;
    }

    private function generateLoginFormHTML(): string
    {
        return '<a href="?messages">Go to message board</a><br />
                <a href="?register">Register a new user</a><br /><br />    
                <form method="post" action=".">
                    <fieldset>
                        <legend>Login - enter Username and password</legend>
                        <p id="' . self::$messageId . '">' . $this->message . '</p>

                        <label for="' . self::$name . '">Username :</label>
                        <input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->storage->getUsername() . '" />

                        <label for="' . self::$password . '">Password :</label>
                        <input type="password" id="' . self::$password . '" name="' . self::$password . '" />

                        <label for="' . self::$keep . '">Keep me logged in  :</label>
                        <input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />

                        <input type="submit" name="' . self::$login . '" value="login" />
                    </fieldset>
                </form>';
    }
}
