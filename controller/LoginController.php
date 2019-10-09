<?php

namespace Controller;

class LoginController extends Controller

{
    private $isLoggedIn = false;
    public function updateState(): void
    {
        try {
            $form = $this->view->getForm();
            $this->isLoggedIn = \Model\UserStorage::validateSession();
            if (!$this->isLoggedIn) {
                $this->loginWithCookies();
            }
            if (!$this->isLoggedIn && $form->getAction() == \Model\FormAction::$login) {
                $this->attemptLogin();
            }
            if ($this->isLoggedIn && $form->getAction() == \Model\FormAction::$logout) {
                $this->logout();
            }
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

    private function loginWithCookies(): void
    {
        $cookies = $this->view->getCookies();
        if ($cookies) $this->validateCookies($cookies);
    }

    private function validateCookies(\Model\Cookies $cookies): void
    {
        //TODO: denna kod körs även i attemptLogin
        $user = \Model\UserStorage::loadUserFromCookies($cookies);
        if ($user) {
            $this->isLoggedIn = true;
            $this->saveSession($user);
            $this->view->setMessage(\Model\Messages::$welcomeWithCookie);
        } else {
            $this->view->setMessage(\Model\Messages::$incorrectCookies);
        }
    }

    private function attemptLogin(): void
    {
        $form = $this->view->getForm();
        $user = \Model\UserStorage::loadUser($form->getUsername(), $form->getPassword());
        if ($user) {
            $this->isLoggedIn = true;
            $this->view->setMessage(\Model\Messages::$welcome);
            $this->saveSession($user);
            $this->view->setCookies($user);
        } else {
            $this->view->setMessage(\Model\Messages::$incorrectCredentials);
        }
    }

    private function logout(): void
    {
        $this->isLoggedIn = false;
        \Model\UserStorage::destroySession();
        $this->view->unsetCookies();
        $this->view->setMessage(\Model\Messages::$logout);
    }


    private function saveSession(\Model\User $user): void
    {
        if ($this->isLoggedIn) {
            \Model\UserStorage::saveSession($user);
        }
    }
}
