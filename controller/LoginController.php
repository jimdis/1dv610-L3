<?php

session_start();

class LoginController
{
    private $isLoggedIn = false;
    private $loginView;
    private $username;
    private $userStorage;
    private $message = '';

    public function __construct(LoginView $loginView)
    {
        $this->loginView = $loginView;
        $this->userStorage = new DOMDocument();
        $this->userStorage->load('users.xml');
    }

    public function updateState(): void
    {
        // $this->loginView->login();
        // $this->isLoggedIn = $this->loginView->getIsLoggedIn();
        if ($this->getRequestMethod() === 'GET') {
            $this->handleGet();
        } else {
            $this->handlePost();
        }
        $this->loginView->setIsLoggedIn($this->isLoggedIn);
        $this->loginView->setMessage($this->message);
    }

    public function getIsLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    private function handleGet(): void
    {
        $this->checkSession();
        if (!$this->isLoggedIn) {
            $this->checkCookie();
        }
    }

    private function handlePost(): void
    {
        $this->checkSession(); // login if session
        // user wants to logout
        if ($this->isLoggedIn && isset($_POST[LoginView::$logout])) {
            $this->logout();
            // user wants to login
        } else if (!$this->isLoggedIn && isset($_POST[LoginView::$name])) {
            $this->attemptLogin();
            if (isset($_POST[LoginView::$keep])) {
                $this->setCookie();
            }
        }
    }

    private function logout(): void
    {
        $this->isLoggedIn = false;
        $this->saveSession(''); // ful-lösning.. Deleta cookie istället..
        $this->destroyCookies();
        $this->message = 'Bye bye!';
    }

    private function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function checkSession(): void
    {
        $session = $_SESSION['session'] ?? null;
        if (
            $session == 'saved' && isset($_SESSION['HTTP_USER_AGENT']) &&
            $_SESSION['HTTP_USER_AGENT'] == md5($_SERVER['HTTP_USER_AGENT'])
        ) {
            $this->isLoggedIn = true;
        }
    }

    private function attemptLogin(): void
    {
        if ($this->validateForm()) {
            $username = $this->loginView->getRequestUserName();
            $password = $this->loginView->getRequestPassword();
            // ändra hårdkodat mot en check mot databas!
            if ($this->authenticateUser($username, $password)) {
                $this->isLoggedIn = true;
                $this->username = $username;
                $this->saveSession($this->loginView->getRequestUserName());
                $this->message = 'Welcome';
            } else {
                $this->message = 'Wrong name or password';
            }
        }
    }

    private function validateForm(): bool
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

    private function checkCookie(): void
    {
        if (isset($_COOKIE[LoginView::$cookieName])) {
            if ($this->validateCookie($_COOKIE[LoginView::$cookieName], $_COOKIE[LoginView::$cookiePassword])) {
                $this->isLoggedIn = true;
                $this->saveSession();
                $this->message = 'Welcome back with cookie';
            }
        }
    }

    private function saveSession(): void
    {
        $_SESSION['session'] = 'saved';
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

        $username = $this->loginView->getRequestUserName(); // sätt tidigare i kedjan!
        $cookiePassword = bin2hex(random_bytes(16));
        $expires = time() + 60 * 60 * 24 * 30; // 30 days
        setcookie(LoginView::$cookieName, $username, $expires);
        setcookie(LoginView::$cookiePassword, $cookiePassword, $expires);

        $user = $this->findUser($username);

        $user->getElementsByTagName('cookiePassword')[0]->textContent = $cookiePassword;
        $user->getElementsByTagName('cookieExpires')[0]->textContent = $expires;

        $this->saveUserStorage();
    }

    private function destroyCookies(): void
    {
        if (isset($_COOKIE[LoginView::$cookieName])) {
            setcookie(LoginView::$cookieName, '', time() - 3600);
        }
        if (isset($_COOKIE[LoginView::$cookiePassword])) {
            setcookie(LoginView::$cookiePassword, '', time() - 3600);
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
        foreach ($this->userStorage->getElementsByTagName('user') as $user) {
            if ($user->getElementsByTagName('name')[0]->textContent == $username) {
                return $user;
            } else return null;
        }
    }

    private function createNewUser(string $username, string $password): void
    {
        $user = $this->userStorage->createElement('user');
        $name = $this->userStorage->createElement('name');
        $password = $this->userStorage->createElement('password');
        $cookiePassword = $this->userStorage->createElement('cookiePassword');
        $cookieExpires = $this->userStorage->createElement('cookieExpires');
        $name->textContent = $username;
        $password->textContent = $password;
        $user->appendChild($name);
        $user->appendChild($password);
        $user->appendChild($cookiePassword);
        $user->appendChild($cookieExpires);
        $this->userStorage->getElementsByTagName('users')[0]->appendChild($user);
        $this->saveUserStorage();
    }

    private function saveUserStorage(): void
    {
        $this->userStorage->save('users.xml');
    }
}
