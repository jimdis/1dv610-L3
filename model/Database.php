<?php

namespace Model;

class Database
{
    private $host;
    private $username;
    private $password;
    private $dbname;

    protected function connect(): \PDO
    {
        $this->host = "localhost";
        $this->username = "root";
        $this->password = "";
        $this->dbname = "weose_l3";
        $pdo = new \PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
        return $pdo;
    }
}
