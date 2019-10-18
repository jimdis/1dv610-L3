<?php

namespace Controller;

class MessageController extends Controller
{
    private $message;

    public function updateState(): void
    {
        $this->updateView();
        $this->storeMessage();
    }

    private function updateView(): void
    {
        // if ($this->storage->getUserIsAuthenticated()) {
        //     $username = $this->storage->getUsername();
        //     $this->view->setUsername($username);
        // }
        if ($this->view->showEditMode()) {
            $this->validateMessageUpdate();
        }
    }

    private function storeMessage()
    {
        if ($this->view->newMessageSubmitted()) {
            $this->message = $this->view->getNewMessage();
            $this->validateMessageAuthor();
            \Model\MessageStorage::storeMessage($this->message);
        } else if ($this->view->messageUpdateSubmitted()) {
            $this->message = $this->view->getNewMessage();
            $this->validateMessageUpdate();
        }
    }


    private function validateMessageAuthor(): void
    {
        if (!$this->storage->getUserIsAuthenticated()) {
            $author = $this->message->author;
            \Model\MessageStorage::validateAuthor($author);
        } else {
            $this->message->setIsVerified(true);
        }
    }

    private function validateMessageUpdate(): void
    {
        $oldMessage = \Model\MessageStorage::getMessageById($this->view->getMessageId());
        if ($oldMessage->author != $this->storage->getUsername()) {
            $this->view->setIsAuthorizedEditor(false);
            throw new \Exception('You cannot edit other people\'s messages!');
        }
    }
}
