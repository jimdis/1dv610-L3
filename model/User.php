<?php

namespace Model;

// require_once("TooShortNameException.php");

class User
{
    private static $minNameLength = 3;
    private static $minPasswordLength = 6;
    private $name;
    private $password;

    public function __construct(string $newName, string $newPassword)
    {

        $this->name = $this->applyFilter($newName);
        $this->password = $newPassword;

        if (strlen($this->name) < self::$minNameLength) {
            throw new \Exception('Username has too few characters, at least 3 characters.');
        }

        if (strlen($this->password) < self::$minPasswordLength) {
            throw new \Exception('Password has too few characters, at least 6 characters.');
        }

        if ($newName != htmlspecialchars($newName)) {
            throw new \Exception('Username contains invalid characters.');
        }
    }

    public function setName(UserName $newName)
    {
        $this->name = $newName->getUserName();
    }

    public function getUserName()
    {
        return $this->name;
    }

    public function hasUserName(): bool
    {
        return $this->name != null;
    }

    public static function applyFilter(string $rawInput): string
    {
        return trim(htmlentities($rawInput));
    }
}
