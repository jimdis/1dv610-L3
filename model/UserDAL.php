<?php

namespace Model;

class UserDAL extends Database
{
    //Todo: vad returneras om result är tomt?
    public function getUser()
    {
        $sql = "SELECT * from user";
        $result = $this->connect()->query($sql);
        if ($result) {
            //TODO: return user?
        } else {
            //TODO: throw error?
        }
        // var_dump($result);
    }

    //TODO: välj returtyp, 
    public function login(string $username, string $password): \Model\User
    {
        $query = $this->connect()->prepare("SELECT username, password FROM user WHERE username = ? AND password = ?");
        $query->execute(array($username, $password));
        $foundUser = $query->fetch(\PDO::FETCH_ASSOC);
        if ($foundUser) {
            $user = new \Model\User($foundUser['username'], $foundUser['password']);
            return $user;
        } else {
            //TODO: skapa eget failedLogin exception
            throw new \Exception('Login failed..');
        }
    }
}
