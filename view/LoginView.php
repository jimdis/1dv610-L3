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
    private $formUserName = '';
    // private $isLoggedIn = false;
    private $message = '';
    private $storage;

    // public function __construct(\Model\UserStorage $storage)
    // {
    //     $this->storage = $storage;
    //     // $this->isLoggedIn = $this->user != null; // fixa
    // }

    public function userWantsToLogin(): bool
    {
        return isset($_POST[self::$login]);
    }

    public function userWantsToLogout(): bool
    {
        return isset($_POST[self::$logout]);
    }

    public function getUserCredentials(): \Model\UserCredentials
    {
        $credentials = new \Model\UserCredentials($_POST[self::$name] ?? '', $_POST[self::$password] ?? '');
        return $credentials;
    }

    private function getUserNameFiltered(): string
    {
        if (isset($_POST[self::$name])) {
            $username = $_POST[self::$name];
            return \Model\User::applyFilter($username);
        }
        return '';
    }

    /**
     * Create HTTP response
     *
     * Should be called after a login attempt has been determined
     *
     * @return  void BUT writes to standard output and cookies!
     */
    public function response(): string
    {

        $response = $this->isLoggedIn ? $this->generateLogoutButtonHTML($this->message) : $this->generateLoginFormHTML($this->message);
        return $response;
    }

    /**
     * Generate HTML code on the output buffer for the logout button
     * @param $message, String output message
     * @return  void, BUT writes to standard output!
     */
    private function generateLogoutButtonHTML($message): string
    {
        return '
			<form  method="post" >
				<p id="' . self::$messageId . '">' . $message . '</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
    }

    /**
     * Generate HTML code on the output buffer for the logout button
     * @param $message, String output message
     * @return  void, BUT writes to standard output!
     */
    private function generateLoginFormHTML($message): string
    {
        return '
            <a href="?register">Register a new user</a><br /><br />    
            <form method="post" action=".">
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>

					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getFormUsername() . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />

                    <input type="submit" name="' . self::$login . '" value="login" />
                    
                    
				</fieldset>
            </form>
		';
    }

    private function getFormUsername(): string
    {
        return $this->getRequestUserName() ? $this->getRequestUserName() : $this->formUserName;
    }

    public function setFormUsername(string $name): void
    {
        $this->formUserName = $name;
    }

    // public function setIsLoggedIn(bool $bool): void
    // {
    //     $this->isLoggedIn = $bool;
    // }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getRequestUserName(): string
    {
        return $_POST[self::$name] ?? '';
    }

    public function getRequestPassword(): string
    {
        return $_POST[self::$password] ?? '';
    }

    // public function loginAttempted(): bool
    // {
    //     return isset($_POST[self::$login]);
    // }

    // public function logoutAttempted(): bool
    // {
    //     return isset($_POST[self::$logout]);
    // }

    // public function getCookieName(): string
    // {
    //     return self::$cookieName;
    // }

    // public function getCookiePassword(): string
    // {
    //     return self::$cookiePassword;
    // }

    public function setCookies(): void
    {
        if (isset($_POST[self::$keep])) {
            $cookies = new \Model\Cookies($this->getRequestUserName());
            setcookie(self::$cookieName, $cookies->getUsername(), $cookies->getExpires());
            setcookie(self::$cookiePassword, $cookies->getPassword(), $cookies->getExpires());
        }
    }

    public function getCookies(): ?\Model\Cookies
    {
        if (isset($_COOKIE[self::$cookieName]) && isset($_COOKIE[self::$cookiePassword])) {
            return new \Model\Cookies($_COOKIE[self::$cookieName], $_COOKIE[self::$cookiePassword]);
        } else return null;
    }

    public function unsetCookies(): void
    {
        if (isset($_COOKIE[self::$cookieName])) {
            setcookie(self::$cookieName, '', time() - 3600);
        }
        if (isset($_COOKIE[self::$cookiePassword])) {
            setcookie(self::$cookiePassword, '', time() - 3600);
        }
    }
}
