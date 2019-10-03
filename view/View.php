<?php

namespace View;

class View
{
    protected $isLoggedIn = false;

    public function setIsLoggedIn(bool $isLoggedIn)
    {
        $this->isLoggedIn = $isLoggedIn;
        var_dump($this->isLoggedIn);
    }
}
