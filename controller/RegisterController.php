<?php

namespace Controller;

class RegisterController extends Controller
{
    private $form;
    private $registerSuccess = false;

    public function updateState(): void
    {
        try {
            $this->form = $this->view->getForm();
            if ($this->form->getAction() == \Model\FormAction::$register) {
                $this->attemptRegisterNewUser();
            }
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
        $this->checkForExistingUsername();
        $this->registerSuccess = true;
    }

    private function checkForExistingUsername(): void
    {
        //TODO: connect to userstorage.
        if ($this->form->getUsername() == 'Admin') {
            throw new \Exception('User exists, pick another username.');
        }
    }
}
