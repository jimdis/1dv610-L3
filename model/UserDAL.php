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

    public function getUserWithToken(\Model\Credentials $credentials): \Model\User
    {
        $date = $this->getFormattedDate(time());
        $sql = "SELECT user.username, user.password FROM user 
                WHERE EXISTS 
                (SELECT userid FROM tokens 
                WHERE tokens.content = ? 
                AND tokens.expires > ? 
                AND user.username = ?
                AND user.id = tokens.userid)";
        $query = $this->connect()->prepare($sql);
        $query->execute([$credentials->getPassword(), $date, $credentials->getUsername()]);
        $foundUser = $query->fetch(\PDO::FETCH_ASSOC);
        if ($foundUser) {
            $user = new \Model\User($foundUser['username'], $foundUser['password']);
            return $user;
        } else {
            throw new \Model\IncorrectCredentialsException();
        }
    }

    public function storeUser(\Model\Credentials $credentials): \Model\User
    {
        $sql = "INSERT INTO user (username, password) VALUES (?, ?)";
        $query = $this->connect()->prepare($sql);
        $success = $query->execute([$credentials->getUsername(), $credentials->getPassword()]);
        if ($success) {
            return new \Model\User($credentials->getUsername(), $credentials->getPassword());
        } else {
            $this->handlePDOError($query->errorInfo());
        }
    }

    public function storeToken(\Model\Token $token, string $username)
    {
        $expires = $this->getFormattedDate($token->getExpires());
        $userId = $this->getUserId($username);
        $sql = "INSERT INTO tokens (userid, content, expires) VALUES (?, ?, ?)";
        $query = $this->connect()->prepare($sql);
        $success = $query->execute([$userId, $token->getContent(), $expires]);
        if (!$success) {
            $this->handlePDOError($query->errorInfo());
        }
    }

    private function getUserId(string $username)
    {
        $sql = "SELECT id FROM user WHERE username = ?";
        $query = $this->connect()->prepare($sql);
        $query->execute([$username]);
        $foundUser = $query->fetch(\PDO::FETCH_ASSOC);
        if ($foundUser) {
            return $foundUser['id'];
        } else {
            throw new \Model\IncorrectCredentialsException();
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

    private function getFormattedDate(int $timestamp): string
    {
        return date('Y-m-d H:i:s', $timestamp);
    }
}
