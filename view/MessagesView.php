<?php

namespace View;

class MessagesView extends View
{
    private static $login = __CLASS__ . '::Login';
    private static $logout = 'LoginView::Logout';
    private static $name = 'LoginView::UserName';
    private static $password = 'LoginView::Password';
    private static $cookieName = 'LoginView::CookieName';
    private static $cookiePassword = 'LoginView::CookiePassword';
    private static $keep = 'LoginView::KeepMeLoggedIn';
    private static $messageId = 'LoginView::Message';
    private $loginUsername = '';
    private $message = '';
    private $messages;


    public function setMessages($messages)
    {
        $this->messages = $messages;
    }
    public function loginFormWasSubmitted(): bool
    {
        return isset($_POST[self::$login]);
    }

    public function getCredentials(): \Model\Credentials
    {
        if ($this->userHasCookies()) {
            $username = $this->getCookieUsername();
            $password = $this->getCookiePassword();
        } else {
            $username = $_POST[self::$name] ?? '';
            $password = $_POST[self::$password] ?? '';
        }
        return new \Model\Credentials($username, $password);
    }

    public function getFormUsername(): string
    {
        return $_POST[self::$name] ?? '';
    }

    public function getFormPassword(): string
    {
        return $_POST[self::$password] ?? '';
    }

    public function logoutWasSubmitted(): bool
    {
        return isset($_POST[self::$logout]);
    }

    public function setLoginUsername(string $username): void
    {
        $this->loginUsername = $username;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
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

    private function getCookieUsername(): string
    {
        return $_COOKIE[self::$cookieName] ?? '';
    }

    private function getCookiePassword(): string
    {
        return $_COOKIE[self::$cookiePassword] ?? '';
    }

    public function unsetCookies(): void
    {
        if ($this->userHasCookies()) {
            setcookie(self::$cookieName, '', time() - 3600);
            setcookie(self::$cookiePassword, '', time() - 3600);
        }
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
        $response = $this->generateMessages();
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


    private function generateMessages(): string
    {
        $messages = '';
        foreach ($this->messages as $message) {
            $messages .= "<li>$message</li>";
        }
        return "<ul>$messages</ul>";
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
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->loginUsername . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />

                    <input type="submit" name="' . self::$login . '" value="login" />
                    
                    
				</fieldset>
            </form>
		';
    }
}
