<?php

namespace Controller;

class RegisterController extends Controller
{
    private $registerSuccess = false;

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

    private function attemptRegisterNewUser(): void
    {
        if ($this->view->userAttemptedRegistration()) {
            $credentials = $this->view->getFormCredentials();
            $this->storage->registerNewUser($credentials);
            $this->registerSuccess = true;
        }
    }
}
