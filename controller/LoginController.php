<?php

namespace Controller;

class LoginController extends Controller

{
    private $isLoggedIn = false;
    public function updateState(): void
    {
        try {
            $this->isLoggedIn = \Model\UserStorage::validateSession();
            if (!$this->isLoggedIn) {
                $this->loginWithCookies();
            }
            if (!$this->isLoggedIn && $this->view->userWantsToLogin()) {
                $this->attemptLogin();
            }
            if ($this->isLoggedIn && $this->view->userWantsToLogout()) {
                $this->logout();
            }
            $this->view->setIsLoggedIn($this->isLoggedIn);
        } catch (\Exception $e) {
            $this->view->setMessage($e->getMessage());
        }
    }

    private function loginWithCookies(): void
    {
        $cookies = $this->view->getCookies();
        if ($cookies) {
            $this->isLoggedIn = \Model\UserStorage::validateCookies($cookies);
            $this->saveSession($cookies->getUsername());
            $this->view->setMessage($this->isLoggedIn ? \Model\Messages::$welcomeWithCookie : \Model\Messages::$incorrectCookies);
        }
    }

    private function attemptLogin(): void
    {
        $credentials = $this->view->getUserCredentials();
        $this->isLoggedIn = \Model\UserStorage::validateUserCredentials($credentials);
        $this->view->setMessage($this->isLoggedIn ? \Model\Messages::$welcome : \Model\Messages::$incorrectCredentials);
        $this->saveSession($credentials->getUsername());
        $this->view->setCookies();
    }

    private function logout(): void
    {
        $this->isLoggedIn = false;
        \Model\UserStorage::destroySession();
        $this->view->unsetCookies();
        $this->view->setMessage(\Model\Messages::$logout);
    }
    public function getIsLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    private function saveSession(string $username): void
    {
        if ($this->isLoggedIn) {
            \Model\UserStorage::saveSession($username);
        }
    }
}
