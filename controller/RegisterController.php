<?php

namespace Controller;

class RegisterController extends Controller
{
    private $registerSuccess = false;
    // private $user;

    public function updateState(): void
    {
        try {
            $this->attemptRegisterNewUser();
        } catch (\Exception $e) {
            $this->view->setMessage($e->getMessage());
        }
    }

    public function getRegisterSuccess(): bool
    {
        return $this->registerSuccess;
    }

    // public function getUser(): \Model\User
    // {
    //     return $this->user;
    // }

    private function attemptRegisterNewUser(): void
    {
        if ($this->view->userAttemptedRegistration()) {
            $username = $this->getUsername();
            $password = $this->getPassword();
            $this->storage->registerNewUser($username, $password);
            $this->registerSuccess = true;
        }
    }
}
