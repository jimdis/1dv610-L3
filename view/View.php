<?php

namespace View;

abstract class View
{
    protected $isLoggedIn = false;
    protected $storage;

    public function __construct(\Model\UserStorage $storage)
    {
        $this->storage = $storage;
    }

    public function setIsLoggedIn(bool $isLoggedIn)
    {
        $this->isLoggedIn = $isLoggedIn;
    }
}
