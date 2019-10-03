<?php

namespace Controller;

class LoginController extends Controller

{
    public function updateState(): void
    {
        if ($this->view->userWantsToLogin()) {
            try {
                $isValidated = \Model\UserStorage::validateUserCredentials($this->view->getUserCredentials());
                var_dump($isValidated);
                if ($isValidated) {
                    $this->view->setIsLoggedIn(true);
                }
            } catch (\Exception $e) {
                $this->view->setMessage($e->getMessage());
            }
        }
    }
}
