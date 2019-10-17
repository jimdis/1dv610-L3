<?php

namespace Controller;

class LoginController extends Controller
{
    public function updateState(): void
    {
        try {
            //TODO: rename these methods..
            // $this->storage->sessionAuth();
            $this->attemptLoginWithCookies();
            $this->attemptLoginWithLoginForm();
            $this->attemptLogout();
        } catch (\Exception $e) {
            $this->view->setMessage($e->getMessage());
        }
    }

    public function updateMessage(string $message): void
    {
        $this->view->setMessage($message);
    }

    public function updateLoginUsername(string $username)
    {
        $this->view->setLoginUsername($username);
    }

    private function attemptLoginWithCookies(): void
    {
        if ($this->userIsAuthenticated()) {
            return;
        }
        if ($this->view->userHasCookies()) {
            $credentials = $this->view->getCredentials();
            $this->storage->loginWithToken($credentials);
            $this->view->setMessage('Welcome back with cookie');
        }
    }

    private function attemptLoginWithLoginForm(): void
    {
        if ($this->userIsAuthenticated()) {
            return;
        }
        if ($this->view->loginFormWasSubmitted()) {
            $username = $this->getUsername();
            $credentials = $this->view->getCredentials();
            // $this->view->setLoginUsername($username); //TODO: Få bort denna rad
            $this->storage->login($credentials);
            $this->view->setMessage('Welcome'); //TODO: Få bort denna rad
            $this->setCookies();
        }
    }

    private function attemptLogout(): void
    {
        if ($this->userIsAuthenticated() && $this->view->logoutWasSubmitted()) {
            // $this->isLoggedIn = false;
            // \Model\UserStorage::destroySession();
            $this->storage->logout();
            $this->view->unsetCookies();
            $this->view->setMessage('Bye bye!');
        }
    }

    // private function saveSession(string $username): void
    // {
    //     if ($this->isLoggedIn) {
    //         \Model\UserStorage::saveSession($username);
    //     }
    // }

    private function setCookies(): void
    {
        if ($this->view->keepLoggedIn()) {
            $token = $this->view->setCookies();
            $this->storage->storeToken($token);
        }
    }
}
