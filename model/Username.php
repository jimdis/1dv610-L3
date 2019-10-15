<?php

namespace Model;

class Username
{
    private $name;

    public function __construct(string $username)
    {
        $this->name = $username;
    }

    public function getUsername(): ?string
    {
        return $this->name;
    }
}
