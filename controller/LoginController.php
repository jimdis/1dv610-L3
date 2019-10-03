<?php

namespace Controller;

class LoginController extends Controller

{
    private $isLoggedIn = false;
    public function updateState(): void
    {
        try {
            $this->isLoggedIn = \Model\UserStorage::validateSession();
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

    private function attemptLogin(): void
    {
        $this->isLoggedIn = \Model\UserStorage::validateUserCredentials($this->view->getUserCredentials());
        $this->view->setMessage($this->isLoggedIn ? \Model\Messages::$welcome : \Model\Messages::$incorrectCredentials);
        $this->saveSession();
    }

    private function logout(): void
    {
        $this->isLoggedIn = false;
        $this->view->setMessage(\Model\Messages::$logout);
    }
    public function getIsLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    private function saveSession(): void
    {
        if ($this->isLoggedIn) {
            \Model\UserStorage::saveSession($this->view->getUserCredentials());
        }
    }
}
