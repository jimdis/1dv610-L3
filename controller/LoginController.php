<?php

session_start();

class LoginController
{
    private $isLoggedIn = false;
    private $loginView;
    private $registerView;
    private $userStorage;
    private $message = '';
    private $currentView;

    public function __construct(LoginView $loginView, RegisterView $registerView)
    {
        $this->loginView = $loginView;
        $this->registerView = $registerView;
        $this->currentView = $loginView;
        $this->userStorage = new DOMDocument();
        $this->userStorage->load('users.xml');
    }

    public function updateState(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->handleGet();
        } else {
            $this->handlePost();
        }

        if ($this->currentView === $this->loginView) {
            $this->loginView->setIsLoggedIn($this->isLoggedIn);
            $this->loginView->setMessage($this->message);
        }
        if ($this->currentView == $this->registerView) {
            $this->registerView->setMessage($this->message);
        }
    }

    private function handleGet(): void
    {
        // set view to register if in query
        if (isset($_GET['register'])) {
            $this->currentView = $this->registerView;
            return;
        }
        // else check session and then cookie
        $this->checkSession();
        if (!$this->isLoggedIn) {
            $this->checkCookie();
        }
    }

    private function handlePost(): void
    {
        // set view to register and handle registration post if in query
        if (isset($_GET['register'])) {
            $this->currentView = $this->registerView;
            $this->attemptRegister();
            return;
        }
        // login if session
        $this->checkSession();
        // user wants to logout
        if ($this->isLoggedIn && $this->loginView->logoutAttempted()) {
            $this->logout();
            return;
        }
        // user wants to login
        if (!$this->isLoggedIn && $this->loginView->loginAttempted()) {
            $this->attemptLogin();
        }
    }

    private function logout(): void
    {
        $this->isLoggedIn = false;
        $_SESSION = array();
        session_destroy();
        $this->destroyCookies();
        $this->message = 'Bye bye!';
    }

    private function checkSession(): void
    {
        if (
            isset($_SESSION['HTTP_USER_AGENT']) &&
            $_SESSION['HTTP_USER_AGENT'] == md5($_SERVER['HTTP_USER_AGENT'])
        ) {
            $this->isLoggedIn = true;
        }
    }

    private function attemptLogin(): void
    {
        if (!$this->validateLoginForm()) {
            return;
        }
        $username = $this->loginView->getRequestUserName();
        $password = $this->loginView->getRequestPassword();
        if ($this->authenticateUser($username, $password)) {
            $this->isLoggedIn = true;
            $this->saveSession();
            $this->setCookie();
            $this->message = 'Welcome';
        } else {
            $this->message = 'Wrong name or password';
        }
    }

    private function validateLoginForm(): bool
    {

        if ($this->loginView->getRequestUserName() === '') {
            $this->message = 'Username is missing';
            return false;
        }
        if ($this->loginView->getRequestPassword() === '') {
            $this->message = 'Password is missing';
            return false;
        }
        return true;
    }

    private function attemptRegister(): void
    {
        if (!$this->validateRegisterForm()) {
            return;
        }
        if ($this->findUser($this->registerView->getRequestUserName())) {
            $this->message = 'User exists, pick another username.';
            return;
        }
        $this->createNewUser($this->registerView->getRequestUserName(), $this->registerView->getRequestPassword());
        $this->message = 'Registered new user.';
        $this->loginView->setFormUsername($this->registerView->getRequestUserName());
        $this->currentView = $this->loginView;
    }

    private function validateRegisterForm(): bool
    {
        $isValidated = true;

        $messageName = '';
        $messagePassword = '';
        $lineBreak = '';

        if (strlen($this->registerView->getRequestUserName()) < 3) {
            $messageName = 'Username has too few characters, at least 3 characters.';
            $isValidated = false;
        }
        if (preg_match("/</", $this->registerView->getRequestUserName())) {
            $messageName = 'Username contains invalid characters.';
            $isValidated = false;
        }
        if (strlen($this->registerView->getRequestPassword()) < 6) {
            $messagePassword = 'Password has too few characters, at least 6 characters.';
            if (!$isValidated) {
                $lineBreak = '<br />';
            }
            $isValidated = false;
        }
        if ($this->registerView->getRequestPassword() !== $this->registerView->getRequestPasswordRepeat()) {
            $messagePassword = 'Passwords do not match.';
            $isValidated = false;
        }



        $this->message = $messageName . $lineBreak . $messagePassword;
        return $isValidated;
    }

    private function checkCookie(): void
    {
        if (!isset($_COOKIE[$this->loginView->getCookieName()])) {
            return;
        }
        if ($this->validateCookie($_COOKIE[$this->loginView->getCookieName()], $_COOKIE[$this->loginView->getCookiePassword()])) {
            $this->isLoggedIn = true;
            $this->saveSession();
            $this->message = 'Welcome back with cookie';
        }
    }

    private function saveSession(): void
    {
        $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
    }

    private function validateCookie(string $cookieName, string $cookiePassword): bool
    {
        $isValidated = false;

        $user = $this->findUser($cookieName);
        // check that user exists, cookiePassword matches & cookie has not expired.
        if ($user && $user->getElementsByTagName('cookiePassword')[0]->textContent == $cookiePassword && (int) $user->getElementsByTagName('cookieExpires')[0]->textContent > time()) {
            $isValidated = true;
        }

        if (!$isValidated) {
            $this->destroyCookies();
            $this->message = 'Wrong information in cookies';
        }

        return $isValidated;
    }

    private function setCookie(): void
    {
        if ($this->loginView->keepLoggedIn()) {

            $username = $this->loginView->getRequestUserName(); // sätt tidigare i kedjan!
            $cookiePassword = bin2hex(random_bytes(16));
            $expires = time() + 60 * 60 * 24 * 30; // 30 days
            setcookie($this->loginView->getCookieName(), $username, $expires);
            setcookie($this->loginView->getCookiePassword(), $cookiePassword, $expires);

            $user = $this->findUser($username);

            $user->getElementsByTagName('cookiePassword')[0]->textContent = $cookiePassword;
            $user->getElementsByTagName('cookieExpires')[0]->textContent = $expires;

            $this->saveUserStorage();
        }
    }

    private function destroyCookies(): void
    {
        if (isset($_COOKIE[$this->loginView->getCookieName()])) {
            setcookie($this->loginView->getCookieName(), '', time() - 3600);
        }
        if (isset($_COOKIE[$this->loginView->getCookiePassword()])) {
            setcookie($this->loginView->getCookiePassword(), '', time() - 3600);
        }
    }



    private function authenticateUser(string $username, string $password): bool
    {

        $user = $this->findUser($username);

        if ($user && $user->getElementsByTagName('password')[0]->textContent == $password) {
            return true;
        }

        return false;
    }

    private function findUser(string $username): ?DOMElement
    {
        $foundUser = null;
        foreach ($this->userStorage->getElementsByTagName('user') as $user) {
            if ($user->getElementsByTagName('name')[0]->textContent == $username) {
                $foundUser = $user;
            }
        }
        return $foundUser;
    }

    private function createNewUser(string $username, string $password): void
    {
        $newUser = $this->userStorage->createElement('user');
        $newName = $this->userStorage->createElement('name');
        $newPassword = $this->userStorage->createElement('password');
        $cookiePassword = $this->userStorage->createElement('cookiePassword');
        $cookieExpires = $this->userStorage->createElement('cookieExpires');
        $newName->textContent = $username;
        $newPassword->textContent = $password;
        $newUser->appendChild($newName);
        $newUser->appendChild($newPassword);
        $newUser->appendChild($cookiePassword);
        $newUser->appendChild($cookieExpires);
        $this->userStorage->getElementsByTagName('users')[0]->appendChild($newUser);
        $this->saveUserStorage();
    }

    private function saveUserStorage(): void
    {
        $this->userStorage->save('users.xml');
    }

    public function getCurrentView(): object
    {
        return $this->currentView;
    }

    public function getIsLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }
}
