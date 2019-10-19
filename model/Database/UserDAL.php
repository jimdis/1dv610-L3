<?php

namespace Model;

class UserDAL
{
    public static function getUser(string $username): \Model\User
    {
        $sql = "SELECT username, password FROM user WHERE username = ?";
        $query = self::connect()->prepare($sql);
        $query->execute([$username]);
        $foundUser = $query->fetch(\PDO::FETCH_ASSOC);
        if ($foundUser) {
            $user = new \Model\User($foundUser['username'], $foundUser['password']);
            return $user;
        } else {
            throw new \Model\IncorrectCredentialsException();
        }
    }

    public static function getUserWithToken(\Model\Credentials $credentials): \Model\User
    {
        $date = self::getFormattedDate(time());
        $sql = "SELECT user.username, user.password FROM user 
                WHERE EXISTS 
                (SELECT userid FROM tokens 
                WHERE tokens.content = ? 
                AND tokens.expires > ? 
                AND user.username = ?
                AND user.id = tokens.userid)";
        $query = self::connect()->prepare($sql);
        $query->execute([$credentials->getPassword(), $date, $credentials->getUsername()]);
        $foundUser = $query->fetch(\PDO::FETCH_ASSOC);
        if ($foundUser) {
            $user = new \Model\User($foundUser['username'], $foundUser['password']);
            return $user;
        } else {
            throw new \Model\IncorrectCredentialsException();
        }
    }

    public static function storeUser(\Model\User $user): void
    {
        $sql = "INSERT INTO user (username, password) VALUES (?, ?)";
        $query = self::connect()->prepare($sql);
        $success = $query->execute([$user->getUsername(), $user->getPassword()]);
        if (!$success) {
            self::handlePDOError($query->errorInfo());
        }
    }

    public static function storeToken(\Model\Token $token, string $username)
    {
        $expires = self::getFormattedDate($token->getExpires());
        $userId = self::getUserId($username);
        $sql = "INSERT INTO tokens (userid, content, expires) VALUES (?, ?, ?)";
        $query = self::connect()->prepare($sql);
        $success = $query->execute([$userId, $token->getContent(), $expires]);
        if (!$success) {
            self::handlePDOError($query->errorInfo());
        }
    }

    public static function doesUsernameExist(string $username): bool
    {
        $sql = "SELECT username FROM user WHERE username = ?";
        $query = self::connect()->prepare($sql);
        $query->execute([$username]);
        $foundUser = $query->fetch(\PDO::FETCH_ASSOC);
        if ($foundUser) {
            return true;
        } else {
            return false;
        }
    }

    private static function getUserId(string $username)
    {
        $sql = "SELECT id FROM user WHERE username = ?";
        $query = self::connect()->prepare($sql);
        $query->execute([$username]);
        $foundUser = $query->fetch(\PDO::FETCH_ASSOC);
        if ($foundUser) {
            return $foundUser['id'];
        } else {
            throw new \Model\IncorrectCredentialsException();
        }
    }

    private static function handlePDOError(array $errorInfo)
    {
        $errorCode = $errorInfo[0];
        $errorMessage = $errorInfo[2];
        if ($errorCode == '23000') {
            throw new \Exception('User exists, pick another username.');
        } else {
            throw new \Exception("There was an error: Code $errorCode: $errorMessage");
        }
    }

    private static function getFormattedDate(int $timestamp): string
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    private static function connect(): \PDO
    {
        $db = new \Model\Database();
        return $db->connect();
    }
}
