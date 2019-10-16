<?php

namespace View;

class MessagesView extends View
{
    private static $submitMessage = __CLASS__ . '::submitMessage';
    private static $name = __CLASS__ . '::UserName';
    private static $messageContent = __CLASS__ . '::messageContent';
    private static $messageId = __CLASS__ . '::Message';
    private $username;
    private $message = '';

    public function getNewMessage(): \Model\Message
    {
        $author = $_POST[self::$name] ?? '';
        $content = $_POST[self::$messageContent] ?? '';
        return new \Model\Message($author, $content);
    }

    // public function setMessages(array $messages)
    // {
    //     $this->messages = $messages;
    // }

    public function messageFormWasSubmitted(): bool
    {
        return isset($_POST[self::$submitMessage]);
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

    public function setUsername(string $username): void
    {
        $this->username = $username;
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
        $response = $this->generateViewHTML();
        return $response;
    }

    private function generateViewHTML(): string
    {
        $form = $this->generateMessageFormHTML($this->message);
        $messageTable = \View\MessagesTable::generateMessagesTableHTML();
        $html = $form . '<br/><h2>Message Board</h2>' . $messageTable;
        return $html;
    }

    private function generateMessageFormHTML($message): string
    {
        $isLoggedIn = $this->storage->getIsAuthenticated();
        $linkText = $isLoggedIn ? 'Account' : 'Go to login';
        $username = $this->username != null ? "<strong>$this->username</strong>" : '';
        $hidden = $this->username != null ? 'hidden' : '';
        return '
        <a href=".">' . $linkText . '</a><br /><br />
            <form method="post" action=""?messages"">
				<fieldset>
					<legend>Write a new message</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
                    
                    <label for="' . self::$name . '">Username :</label>
                    ' . $username . '
					<input ' . $hidden . ' type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->username . '" />
                    <br/>
					<label for="' . self::$messageContent . '">Your message :</label><br/>
					<textarea rows=6 cols=50" id="' . self::$messageContent . '" name="' . self::$messageContent . '">Type your message here..</textarea>
                    <br/>
                    <input type="submit" name="' . self::$submitMessage . '" value="Submit" />
                    
                    
				</fieldset>
            </form>
		';
    }
}
