<?php

class LoginController
{
    private $isLoggedIn = false;
    private $loginView;

    public function __construct(LoginView $loginView)
    {
        $this->loginView = $loginView;
    }

    public function updateState(): void
    {
        $this->loginView->login();
        $this->isLoggedIn = $this->loginView->getIsLoggedIn();
    }

    public function getIsLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }
}
