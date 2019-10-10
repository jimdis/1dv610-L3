<?php

namespace Model;

class Cookie
{
    private static $EXPIRY_IN_DAYS = 30;
    private $content;
    private $expires;


    public function __construct(string $content = null)
    {
        $this->content = $content;
        $this->expires = time() + 60 * 60 * 24 * self::$EXPIRY_IN_DAYS;
        if ($this->content == null) {
            $this->content = md5(time());
            var_dump($this->content);
        }
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
