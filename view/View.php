<?php

namespace View;

abstract class View
{
    protected $storage;
    protected $message = '';

    public function __construct(\Model\UserStorage $storage)
    {
        $this->storage = $storage;
    }

    protected abstract function show(): string;

    public function setMessage(string $message)
    {
        $this->message = $message;
    }
}
