<?php

namespace Model;

class UserDAL extends Database
{
    //TODO: bättre felhantering?
    public function getUser(string $username): \Model\User
    {
        $sql = "SELECT username, password FROM user WHERE username = ?";
        $query = $this->connect()->prepare($sql);
        $query->execute([$username]);
        $foundUser = $query->fetch(\PDO::FETCH_ASSOC);
        if ($foundUser) {
            $user = new \Model\User($foundUser['username'], $foundUser['password']);
            return $user;
        } else {
            throw new \Model\IncorrectCredentialsException();
        }
    }

    public function register(string $username, string $password)
    {
        $sql = "INSERT INTO user (username, password) VALUES (?, ?)";
        $query = $this->connect()->prepare($sql);
        $success = $query->execute([$username, $password]);
        if (!$success) {
            $this->handlePDOError($query->errorInfo());
        }
    }

    //TODO: custom exception, handle error message in UserStorage?
    private function handlePDOError(array $errorInfo)
    {
        $errorCode = $errorInfo[0];
        $errorMessage = $errorInfo[2];
        if ($errorCode == '23000') {
            throw new \Exception('User exists, pick another username.');
        } else {
            throw new \Exception("There was an error: Code $errorCode: $errorMessage");
        }
    }
}
