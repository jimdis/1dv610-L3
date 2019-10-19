<?php

namespace View;

class RegisterView extends View
{
    public static $register = 'RegisterView::Register';
    public static $name = 'RegisterView::UserName';
    public static $password = 'RegisterView::Password';
    public static $passwordRepeat = 'RegisterView::PasswordRepeat';
    public static $showLoginView = 'RegistrationView::showLoginView';
    private static $messageId = 'RegisterView::Message';

    public function userAttemptedRegistration(): bool
    {
        return isset($_POST[self::$register]);
    }

    public function getFormCredentials(): \Model\Credentials
    {
        $username = $_POST[self::$name] ?? '';
        $password = $this->getFormPassword();
        return new \Model\Credentials($username, $password);
    }

    public function show(): string
    {
        return '
            <a href=".">Back to login</a><br /><br />
            <form method="post" action="?' . \Model\Routes::$register . '">
				<fieldset>
					<legend>Register a new user - Write username and password</legend>
					<p id="' . self::$messageId . '">' . $this->message . '</p>

					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getFormUsername() . '" />

					<label for="' . self::$password . '">Password :</label>
                    <input type="password" id="' . self::$password . '" name="' . self::$password . '" />
                    
                    <label for="' . self::$passwordRepeat . '">Repeat password :</label>
					<input type="password" id="' . self::$passwordRepeat . '" name="' . self::$passwordRepeat . '" />

                    <input type="submit" name="' . self::$register . '" value="register" />
                    
				</fieldset>
            </form>
        ';
    }

    private function getFormUsername(): string
    {
        return isset($_POST[self::$name])
            ? \Model\SanitizeInput::sanitize($_POST[self::$name])
            : '';
    }

    private function getFormPassword(): string
    {
        $password = $_POST[self::$password] ?? '';
        $passwordRepeat = $_POST[self::$passwordRepeat] ?? '';
        if ($password == $passwordRepeat) {
            return $password;
        } else {
            throw new \Exception('Passwords do not match.');
        }
    }
}
