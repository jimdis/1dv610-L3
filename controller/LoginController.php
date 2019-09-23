<?php

session_start();

class LoginController
{
    private $isLoggedIn = false;
    private $loginView;
    private $username;
    private $password;
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
        // $this->checkCookie(); // login & load cookie vars if cookie
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
        if (strlen($this->loadSession()) > 0) { // ändra så den jämför med username
            $this->isLoggedIn = true;
        }
    }

    private function attemptLogin(): void
    {
        if ($this->validateForm()) {
            $username = $this->loginView->getRequestUserName();
            $password = $this->loginView->getRequestPassword();
            // ändra hårdkodat mot en check mot databas!
            if ($username === 'Admin' && $password === 'Password') {
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
            $this->cookieName = $_COOKIE[LoginView::$cookieName];
            $this->cookiePassword = $_COOKIE[LoginView::$cookiePassword];
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
            $this->destroyCookies();
            $this->message = 'Wrong information in cookies';
        }

        return $passwordMatch;
    }

    private function setCookie(): void
    {

        $username = $this->loginView->getRequestUserName(); // sätt tidigare i kedjan!
        $cookiePassword = bin2hex(random_bytes(16));
        setcookie(LoginView::$cookieName, $username, time() + 60 * 60 * 24 * 30); // 30 days
        setcookie(LoginView::$cookiePassword, $cookiePassword, time() + 60 * 60 * 24 * 30); // 30 days

        foreach ($this->userStorage->getElementsByTagName('user') as $user) {
            if ($user->getElementsByTagName('name')[0]->textContent == $username) {
                if (!$user->getElementsByTagName('cookiePassword')[0]) {
                    $newPassword = $this->userStorage->createElement('cookiePassword');
                    $newPassword->textContent = $cookiePassword;
                    $user->appendChild($newPassword);
                } else {
                    $user->getElementsByTagName('cookiePassword')[0]->textContent = $cookiePassword;
                }
            }

            $this->saveUserStorage();
        }
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

    private function saveUserStorage(): void
    {
        $this->userStorage->save('users.xml');
    }
}
