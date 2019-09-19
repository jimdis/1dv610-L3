<?php
session_start();

class LoginView
{
    private static $login = 'LoginView::Login';
    private static $logout = 'LoginView::Logout';
    private static $name = 'LoginView::UserName';
    private static $password = 'LoginView::Password';
    private static $cookieName = 'LoginView::CookieName';
    private static $cookiePassword = 'LoginView::CookiePassword';
    private static $keep = 'LoginView::KeepMeLoggedIn';
    private static $messageId = 'LoginView::Message';
    private $isLoggedIn = false;
    private $isFirstLogin = false;
    private $isFirstLogout = false;

    /**
     * Create HTTP response
     *
     * Should be called after a login attempt has been determined
     *
     * @return  void BUT writes to standard output and cookies!
     */
    public function response()
    {

        $users = $this->loadUsers();

        // foreach ($users->getElementsByTagName('user') as $user) {
        //     if ($user->getElementsByTagName('name')[0]->textContent == 'Jim') {
        //         var_dump($user->getElementsByTagName('password')[0]->textContent);
        //     }
        // }




        // $findPassword = '';

        // foreach ($users as $user) {
        //     if ($user->name == 'Jim') {
        //         $findPassword = $user->password;
        //         break;
        //     }
        // }

        $message = '';

        if ($this->isLoggedIn && $this->isFirstLogin) {
            $message = 'Welcome';
            if (isset($_POST[self::$keep])) {
                $message .= ' and you will be remembered';
            }
        }

        if (isset($_COOKIE[self::$cookieName]) && $this->isFirstLogin) {
            $message = 'Welcome back with cookie';
        }

        if (!$this->isLoggedIn && $this->userAttemptLogin()) {
            $message = $this->validateForm();
        }

        if (!$this->isLoggedIn && $this->isFirstLogout) {
            $message = 'Bye bye!';
        }

        $response = $this->isLoggedIn ? $this->generateLogoutButtonHTML($message) : $this->generateLoginFormHTML($message);
        return $response;
    }

    /**
     * Generate HTML code on the output buffer for the logout button
     * @param $message, String output message
     * @return  void, BUT writes to standard output!
     */
    private function generateLogoutButtonHTML($message)
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
    private function generateLoginFormHTML($message)
    {
        return '
			<form method="post" >
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>

					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getRequestUserName() . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />

					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
    }

    private function getRequestUserName(): string
    {
        return $_POST[self::$name] ?? '';
    }

    private function getRequestPassword(): string
    {
        return $_POST[self::$password] ?? '';
    }

    private function userAttemptLogin(): bool
    {
        return !$this->logout() && $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    private function logout(): bool
    {
        return isset($_POST[self::$logout]) ? true : false;
    }

    public function getIsLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    public function login(): void
    {
        if ($this->logout()) {
            if (strlen($this->loadSession()) > 0) {
                $this->isFirstLogout = true;
            }
            $this->isLoggedIn = false;
            $this->saveSession(''); // ful-lösning.. Deleta cookie istället..

        } else if (strlen($this->loadSession()) > 0) {
            $this->isLoggedIn = true;
        } else if (isset($_COOKIE[self::$cookieName])) {
            $this->isLoggedIn = true;
            $this->isFirstLogin = true;
            $this->saveSession($_COOKIE[self::$cookieName]);
        } else if ($this->getRequestUserName() === 'Admin' && $this->getRequestPassword() === 'Password') {
            $this->isFirstLogin = true;
            $this->isLoggedIn = true;
            $this->saveSession($this->getRequestUserName());
            $this->setCookie();
        }
    }

    private function validateForm(): string
    {

        if ($this->getRequestUserName() === '') {
            return 'Username is missing';
        }
        if ($this->getRequestPassword() === '') {
            return 'Password is missing';
        }
        return 'Wrong name or password';
    }

    private function saveSession(string $toBeSaved): void
    {
        $_SESSION['session'] = $toBeSaved;
    }

    private function loadSession(): string
    {
        return $_SESSION['session'] ?? '';
    }

    private function setCookie(): void
    {
        if (isset($_POST[self::$keep])) {

            $username = $this->getRequestUserName();
            $cookiePassword = bin2hex(random_bytes(16));
            setcookie(self::$cookieName, $username, time() + 60 * 60 * 24 * 30); // 30 days
            setcookie(self::$cookiePassword, $cookiePassword, time() + 60 * 60 * 24 * 30); // 30 days
            $users = $this->loadUsers();

            foreach ($users->getElementsByTagName('user') as $user) {
                if ($user->getElementsByTagName('name')[0]->textContent == $username) {
                    if (!$user->getElementsByTagName('cookiePassword')[0]) {
                        $newPassword = $users->createElement('cookiePassword');
                        $newPassword->textContent = $cookiePassword;
                        $user->appendChild($newPassword);
                    } else {
                        $user->getElementsByTagName('cookiePassword')[0]->textContent = $cookiePassword;
                    }
                }
            }

            $this->saveUsers($users);
        }
    }

    private function loadUsers(): DOMDocument
    {
        // $users = simplexml_load_file('users.xml');
        $users = new DOMDocument();
        $users->load('users.xml');
        return $users;
    }

    private function saveUsers(DOMDocument $users): void
    {
        $users->save('users.xml');
    }
}
