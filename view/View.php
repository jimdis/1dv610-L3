<?php

namespace View;

abstract class View
{
    protected $isLoggedIn = false;

    public function setIsLoggedIn(bool $isLoggedIn)
    {
        $this->isLoggedIn = $isLoggedIn;
    }
}
