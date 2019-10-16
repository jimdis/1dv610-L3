<?php

namespace Model;

class Message
{
    private $author;
    private $content;
    private $isEditable = false;

    public function __construct(string $author, string $content)
    {
        if (strlen($author) == 0) {
            throw new \Exception('Please enter a username');
        }
        if (strlen($content) == 0) {
            throw new \Exception('Your message is empty!');
        }
        $this->author = $author;
        $this->content = $content;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setIsEditable(bool $bool): void
    {
        $this->isEditable = $bool;
    }

    public function getIsEditable(): bool
    {
        return $this->isEditable;
    }
}
