<?php

namespace Controller;

class LoginController extends Controller
{
    private $isLoggedIn = false;
    private $formAction;

    public function updateState(): void
    {
        try {
            //TODO: rename these methods..
            $this->formAction = $this->view->getFormAction();
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

    private function attemptLoginWithSession(): void
    {
        $this->isLoggedIn = \Model\UserStorage::validateSession();
    }

    private function attemptLoginWithCookies(): void
    {
        if ($this->isLoggedIn) {
            return;
        }

        $cookies = $this->view->getCookies();
        if ($cookies) {
            $this->isLoggedIn = \Model\UserStorage::validateCookies($cookies);
            $this->saveSession($cookies->getUsername());
            $this->view->setMessage($this->isLoggedIn ? \Model\Messages::$welcomeWithCookie : \Model\Messages::$incorrectCookies);
        }
    }

    private function attemptLoginWithLoginForm(): void
    {
        if (!$this->isLoggedIn && $this->formAction == \Model\FormAction::$login) {

            $username = $this->view->getFormUsername();
            $password = $this->view->getFormPassword();
            $form = new \Model\LoginCredentials($username, $password);
            $this->isLoggedIn = \Model\UserStorage::validateUserCredentials($form);
            $this->view->setMessage(
                $this->isLoggedIn
                    ? \Model\Messages::$welcome
                    : \Model\Messages::$incorrectCredentials
            );
            $this->saveSession($form->getUsername());
            $this->setCookies();
        }
    }

    private function attemptLogout(): void
    {
        if ($this->isLoggedIn && $this->formAction == \Model\FormAction::$logout) {
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
