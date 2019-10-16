<?php

namespace Model;

class Message
{
    private $author;
    private $content;
    private $isEditable = false;
    private $id;

    public function __construct(string $author, string $content, $id = null)
    {
        if (strlen($author) == 0) {
            throw new \Exception('Please enter a username');
        }
        if (strlen($content) == 0) {
            throw new \Exception('Your message is empty!');
        }
        $this->author = $author;
        $this->content = $content;
        $this->id = $id;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function setIsEditable(bool $bool): void
    {
        $this->isEditable = $bool;
    }
}
