<?php

namespace Controller;

class RegisterController extends Controller
{
    private $form;
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
            $username = $this->getUsername();
            $password = $this->getPassword();
            \Model\UserStorage::registerNewUser($username, $password);
            $this->registerSuccess = true;
        }
    }

    private function checkForExistingUsername(): void
    {
        //TODO: connect to userstorage.
        if ($this->form->getUsername() == 'Admin') {
            throw new \Exception('User exists, pick another username.');
        }
    }
}
