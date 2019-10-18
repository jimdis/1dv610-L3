<?php

namespace Controller;

class MessageController extends Controller
{
    public function updateState(): void
    {
        if ($this->storage->getUserIsAuthenticated()) {
            $username = $this->storage->getUsername();
            $this->view->setUsername($username);
        }
        $this->storeMessage();
    }

    private function storeMessage()
    {
        if ($this->view->newMessageSubmitted()) {
            $message = $this->view->getNewMessage();
            $this->validateMessageAuthor($message);
            \Model\MessageStorage::storeMessage($message);
        } else if ($this->view->messageUpdateSubmitted()) {
            $message = $this->view->getNewMessage();
            $this->validateMessageAuthor($message);
            \Model\MessageStorage::updateMessage($message);
        }
    }


    private function validateMessageAuthor(\Model\Message $message)
    {
        if (!$this->storage->getUserIsAuthenticated()) {
            $author = $message->author;
            \Model\MessageStorage::validateAuthor($author);
        } else {
            $message->setIsVerified(true);
        }
    }
}
