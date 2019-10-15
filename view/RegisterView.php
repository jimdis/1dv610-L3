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
    private $message = '';

    // public function getForm(): \Model\RegisterForm
    // {
    //     $action = $this->getFormAction();
    //     $username = $this->getUsername();
    //     $password = $this->getPassword();
    //     $passwordRepeat = $this->getPasswordRepeat();
    //     $form = new \Model\RegisterForm($action, $username, $password, $passwordRepeat);
    //     return $form;
    // }

    public function userAttemptedRegistration(): bool
    {
        if (isset($_POST[self::$register])) {
            return true;
        } else {
            return false;
        }
    }

    //TODO: same as in LoginView.. Create FormView parent?
    public function getFormCredentials(): \Model\Credentials
    {
        $username = $_POST[self::$name] ?? '';
        $password = $this->getFormPassword();
        return new \Model\Credentials($username, $password);
    }

    public function getFormUserName(): string
    {
        return $_POST[self::$name] ?? '';
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

    // private function getPasswordRepeat(): string
    // {
    //     return $_POST[self::$passwordRepeat] ?? '';
    // }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

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
            <a href=".">Back to login</a><br /><br />
            <form method="post" action="?register">
				<fieldset>
					<legend>Register a new user - Write username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>

					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . strip_tags($this->getFormUserName()) . '" />

					<label for="' . self::$password . '">Password :</label>
                    <input type="password" id="' . self::$password . '" name="' . self::$password . '" />
                    
                    <label for="' . self::$passwordRepeat . '">Repeat password :</label>
					<input type="password" id="' . self::$passwordRepeat . '" name="' . self::$passwordRepeat . '" />

                    <input type="submit" name="' . self::$register . '" value="register" />
                    
				</fieldset>
            </form>
        ';
    }
}
