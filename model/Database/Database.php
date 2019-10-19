<?php

namespace Model;

class Database
{
    private $host;
    private $username;
    private $password;
    private $dbname;

    public function connect(): \PDO
    {
        $this->host = \Config::$DB_HOST;
        $this->dbname = \Config::$DB_NAME;
        $this->username = \Config::getDBUsername();
        $this->password = \Config::getDBPassword();

        $pdo = new \PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
        return $pdo;
    }
}
