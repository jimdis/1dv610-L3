<?php

namespace Model;

class Message
{
    private $author;
    private $content;
    private $isVerified = false;
    private $id;
    private $updated;

    public function __construct(string $author, string $content, int $id = null, string $updated = null)
    {
        if (strlen($author) == 0) {
            throw new \Exception('Please enter a username');
        }
        if (strlen($content) == 0) {
            throw new \Exception('Your message is empty!');
        }

        if (strlen($content) > 1000) {
            throw new \Exception('Your message exceeds maximum length (1000 characters)!');
        }
        $this->author = \Model\SanitizeInput::sanitize($author);
        $this->content = \Model\SanitizeInput::sanitize($content);
        $this->id = $id;
        $this->updated = $updated;
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
