<?php

namespace Controller;

class LoginController extends Controller

{
    private $isLoggedIn = false;
    public function updateState(): void
    {
        if ($this->view->userWantsToLogin()) {
            try {
                $this->isLoggedIn = \Model\UserStorage::validateUserCredentials($this->view->getUserCredentials());
                $this->view->setIsLoggedIn($this->isLoggedIn);
                $this->view->setMessage($this->isLoggedIn ? \Model\Messages::$welcome : \Model\Messages::$incorrectCredentials);
            } catch (\Exception $e) {
                $this->view->setMessage($e->getMessage());
            }
        }
    }
    public function getIsLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }
}
