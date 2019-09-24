<?php

class RegisterView
{
    public static $register = 'RegisterView::Register';
    public static $name = 'RegisterView::UserName';
    public static $password = 'RegisterView::Password';
    public static $passwordRepeat = 'RegisterView::PasswordRepeat';
    public static $showLoginView = 'RegistrationView::showLoginView';
    private static $messageId = 'RegisterView::Message';
    private $isLoggedIn = false;
    private $message = '';


    /**
     * Create HTTP response
     *
     * Should be called after a login attempt has been determined
     *
     * @return  void BUT writes to standard output and cookies!
     */
    public function response()
    {

        return $this->generateRegisterFormHTML($this->message);
    }

    /**
     * Generate HTML code on the output buffer for the logout button
     * @param $message, String output message
     * @return  void, BUT writes to standard output!
     */
    private function generateRegisterFormHTML($message)
    {
        return '
			<form method="post" action="?register">
				<fieldset>
					<legend>Register a new user - Write username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>

					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getRequestUserName() . '" />

					<label for="' . self::$password . '">Password :</label>
                    <input type="password" id="' . self::$password . '" name="' . self::$password . '" />
                    
                    <label for="' . self::$passwordRepeat . '">Password :</label>
					<input type="password" id="' . self::$passwordRepeat . '" name="' . self::$passwordRepeat . '" />

                    <input type="submit" name="' . self::$register . '" value="register" />
                    
				</fieldset>
            </form>
            <a href="."><button>Back to login</button></a>
        ';
    }

    public function getRequestUserName(): string
    {
        return $_POST[self::$name] ?? '';
    }

    public function getRequestPassword(): string
    {
        return $_POST[self::$password] ?? '';
    }
}
