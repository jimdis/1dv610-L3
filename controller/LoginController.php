<?php

namespace Controller;

class LoginController extends Controller
{
    private $isLoggedIn = false;
    // private $formAction;

    public function updateState(): void
    {
        try {
            //TODO: rename these methods..
            // $this->formAction = $this->view->getFormAction();
            $this->attemptLoginWithSession();
            $this->attemptLoginWithCookies();
            $this->attemptLoginWithLoginForm();
            $this->attemptLogout();
            $this->view->setIsLoggedIn($this->isLoggedIn);
        } catch (\Exception $e) {
            $this->view->setMessage($e->getMessage());
        }
    }

    public function getIsLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    public function updateMessage(string $message): void
    {
        $this->view->setMessage($message);
    }

    public function updateLoginUsername(string $username)
    {
        $this->view->setLoginUsername($username);
    }

    private function attemptLoginWithSession(): void
    {
        $this->isLoggedIn = \Model\UserStorage::validateSession();
    }

    private function attemptLoginWithCookies(): void
    {
        if (!$this->isLoggedIn && $this->view->userHasCookies()) {
            $cookieUsername = $this->view->getCookieUsername();
            $cookiePassword = $this->view->getCookiePassword();
            $this->isLoggedIn = \Model\UserStorage::validateCookies($cookieUsername, $cookiePassword);
            $this->saveSession($cookieUsername);
            $this->view->setMessage($this->isLoggedIn ? \Model\Messages::$welcomeWithCookie : \Model\Messages::$incorrectCookies);
        }
    }

    private function attemptLoginWithLoginForm(): void
    {
        if (!$this->isLoggedIn && $this->view->loginFormWasSubmitted()) {
            $username = $this->getUsername();
            $password = $this->getPassword();
            $this->view->setFormUsername($username);
            // TODO: do something with user object..
            $user = \Model\UserStorage::loginUser($username, $password);
            $this->isLoggedIn = true;
            $this->view->setMessage(\Model\Messages::$welcome);
            $this->saveSession($username);
            $this->setCookies();
        }
    }

    private function attemptLogout(): void
    {
        if ($this->isLoggedIn && $this->view->logoutWasSubmitted()) {
            $this->isLoggedIn = false;
            \Model\UserStorage::destroySession();
            $this->view->unsetCookies();
            $this->view->setMessage(\Model\Messages::$logout);
        }
    }

    private function saveSession(string $username): void
    {
        if ($this->isLoggedIn) {
            \Model\UserStorage::saveSession($username);
        }
    }

    private function setCookies(): void
    {
        // TODO: control cookie login here. Ask view, tell view. No login in view.
        $this->view->setCookies();
    }
}
