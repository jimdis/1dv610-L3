<?php

namespace Model;

class User extends Database
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
    public function login(string $username, string $password)
    {
        $query = $this->connect()->prepare("SELECT id FROM user WHERE username = ? AND password = ?");
        $query->execute(array($username, $password));
        if ($query) {
            return $query->fetchColumn();
        } else {
            return null;
            //TODO: kasta undantag?
        }
    }
}
