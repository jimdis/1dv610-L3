<?php

session_start();

class LoginController
{
    private $isLoggedIn = false;
    private $loginView;
    private $username;
    private $password;
    // private $isFirstLogin;
    private $userStorage;
    private $cookieName;
    private $cookiePassword;
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
            $this->checkSession();
        }
        $this->loginView->setIsLoggedIn($this->isLoggedIn);
        $this->loginView->setMessage($this->message);
    }

    public function getIsLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    private function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function checkSession(): void
    {
        if (strlen($this->loadSession()) > 0) { // ändra så den jämför med username
            $this->isLoggedIn = true;
        } else {
            $this->checkCookie();
        }
    }

    private function checkCookie(): void
    {
        if (isset($_COOKIE[$this->loginView->getCookieName()])) {
            $this->cookieName = $_COOKIE[$this->loginView->getCookieName()];
            $this->cookiePassword = $_COOKIE[$this->loginView->getCookiePassword()];
            if ($this->validateCookie()) {
                $this->isLoggedIn = true;
                $this->saveSession('Admin'); //replace med $this->username
                $this->message = 'Welcome back with cookie';
            }
        }
    }

    private function saveSession(string $toBeSaved): void
    {
        $_SESSION['session'] = $toBeSaved; // ändra till username
    }

    private function loadSession(): string
    {
        return $_SESSION['session'] ?? '';
    }

    private function validateCookie(): bool
    {
        $passwordMatch = false;

        foreach ($this->userStorage->getElementsByTagName('user') as $user) {
            if ($user->getElementsByTagName('name')[0]->textContent == $this->cookieName) {
                if ($user->getElementsByTagName('cookiePassword')[0]->textContent == $this->cookiePassword) {
                    $passwordMatch = true;
                }
            }
        }

        if (!$passwordMatch) {
            setcookie($_COOKIE[$this->loginView->getCookieName()], $this->cookieName, time() - 1000);
            setcookie($_COOKIE[$this->loginView->getCookiePassword()], $this->cookiePassword, time() - 1000);
        }

        return $passwordMatch;
    }
}
