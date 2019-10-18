<?php

namespace Controller;

class LoginController extends Controller
{
    public function updateState(): void
    {
        if ($this->storage->getUserIsAuthenticated()) {
            $this->handleLogout();
        } else {
            $this->handleLogin();
        }
    }

    private function handleLogout(): void
    {
        if ($this->view->logoutWasSubmitted()) {
            $this->storage->logout();
            $this->view->unsetCookies();
            $this->view->setMessage('Bye bye!');
        }
    }

    private function handleLogin(): void
    {
        $credentials = $this->view->getCredentials();
        if ($this->view->userHasCookies()) {
            $this->handleCookies($credentials);
        } else if ($this->view->loginFormWasSubmitted()) {
            $this->handleLoginForm($credentials);
        }
    }

    private function handleCookies(\Model\Credentials $credentials): void
    {
        $this->storage->loginWithToken($credentials);
        $this->view->setMessage('Welcome back with cookie');
    }

    private function handleLoginForm(\Model\Credentials $credentials): void
    {
        $this->storage->login($credentials);
        $this->view->setMessage('Welcome');
        $this->setCookies();
    }

    private function setCookies(): void
    {
        if ($this->view->keepLoggedIn()) {
            $token = $this->view->setCookies();
            $this->storage->storeToken($token);
        }
    }
}
