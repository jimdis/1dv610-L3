<?php

namespace Model;

// require_once("TooShortNameException.php");

class User
{
    private static $minNameLength = 3;
    private $name = null;

    public function __construct(string $newName)
    {

        $this->name = $this->applyFilter($newName);

        if (strlen($this->name) < self::$minNameLength) {
            throw new \Exception('Username has too few characters, at least 3 characters.');
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
