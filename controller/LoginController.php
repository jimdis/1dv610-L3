<?php


class LoginController {
    private $isLoggedIn = false;

    public function login(string $username, string $password) {
        if ($username === 'Admin' && $password === 'Password') {
            $this->isLoggedIn = true;
        } else {
            $this->isLoggedIn = false;
        }
    }

    public function getIsLoggedIn() {
        return $this->isLoggedIn;
    }

}