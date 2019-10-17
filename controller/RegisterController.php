<?php

namespace Controller;

class RegisterController extends Controller
{
    private $redirect = false;

    public function updateState(): void
    {
        try {
            $this->attemptRegisterNewUser();
        } catch (\Exception $e) {
            $this->view->setMessage($e->getMessage());
        }
    }

    public function getRedirect(): bool
    {
        return $this->redirect;
    }

    private function attemptRegisterNewUser(): void
    {
        if ($this->view->userAttemptedRegistration()) {
            $credentials = $this->view->getFormCredentials();
            $this->storage->registerNewUser($credentials);
            $this->redirect = true;
        }
    }
}
