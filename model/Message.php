<?php

namespace Model;

class Message
{
    private $author;
    private $content;
    private $isVerified = false;
    private $id;

    public function __construct(string $author, string $content, $id = null)
    {
        if (strlen($author) == 0) {
            throw new \Exception('Please enter a username');
        }
        if (strlen($content) == 0) {
            throw new \Exception('Your message is empty!');
        }
        $this->author = \Model\SanitizeInput::sanitize($author);
        $this->content = \Model\SanitizeInput::sanitize($content);
        $this->id = $id;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function setIsVerified(bool $bool): void
    {
        $this->isVerified = $bool;
    }
}
