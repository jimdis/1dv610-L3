<?php

namespace Controller;

class MessagesController extends Controller
{
    private $messages = [];

    public function updateState(): void
    {
        try {
            //TODO: Remove below
            array_push($this->messages, new \Model\Message('jim', 'Hello World!'));
            $this->updateView();
        } catch (\Exception $e) {
            $this->view->setMessage($e->getMessage());
        }
    }

    private function updateView()
    {
        if ($this->storage->getIsAuthenticated()) {
            $username = $this->storage->getUser()->getUsername();
            $this->view->setUsername($username);
        }
        $this->storeMessage();
        // $messages = $this->getMessages();
        // $this->view->setMessages($messages);
    }

    private function storeMessage()
    {
        if ($this->view->newMessageSubmitted()) {
            $message = $this->view->getNewMessage();
            $this->validateMessageAuthor($message);
            \Model\MessageStorage::storeNewMessage($message);
        } else if ($this->view->messageUpdateSubmitted()) {
            $message = $this->view->getNewMessage();
            $this->validateMessageAuthor($message);
            \Model\MessageStorage::updateMessage($message->id);
        }
    }


    private function validateMessageAuthor(\Model\Message $message)
    {
        if (!$this->storage->getIsAuthenticated()) {
            $author = $message->getAuthor();
            \Model\MessageStorage::validateAuthor($author);
        } else {
            $message->setIsEditable(true);
        }
    }
}
