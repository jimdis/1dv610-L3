<?php

namespace View;

abstract class View
{
    protected $storage;

    public function __construct(\Model\UserStorage $storage)
    {
        $this->storage = $storage;
    }
}
