<?php

namespace Model;

class Token
{
    private static $EXPIRY_IN_DAYS = 30;
    private $content;
    private $expires;


    public function __construct()
    {
        $this->content = bin2hex(random_bytes(16));
        $this->expires = time() + 60 * 60 * 24 * self::$EXPIRY_IN_DAYS;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getExpires(): int
    {
        return $this->expires;
    }
}
