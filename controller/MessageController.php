<?php

namespace Controller;

class MessageController extends Controller
{
    private $message;

    public function updateState(): void
    {
        $this->handleNewMessage();
        $this->handleMessageUpdate();
        $this->handleMessageDelete();
    }

    private function handleNewMessage()
    {
        if ($this->view->newMessageSubmitted()) {
            $this->message = $this->view->getNewMessage();
            $this->validateMessageAuthor();
            \Model\MessageDAL::storeMessage($this->message);
            $this->view->setMessage('Your message was submitted!');
        } else if ($this->view->messageUpdateSubmitted()) {
            $this->message = $this->view->getNewMessage();
            $this->handleMessageUpdate();
            \Model\MessageDAL::updateMessage($this->message);
            $this->view->setMessage('Your message was updated!');
        }
        $this->view->setContentInput('');
    }


    private function validateMessageAuthor(): void
    {
        if (!$this->storage->getUserIsAuthenticated()) {
            $author = $this->message->author;
            \Model\MessageDAL::validateAuthor($author);
        } else {
            $this->message->setIsVerified(true);
        }
    }

    private function handleMessageUpdate(): void
    {
        if (\View\MessageTable::userWantsToEditMessage()) {
            $this->view->setShowEditForm(true);
            $this->view->setEditMessageId(\View\MessageTable::getMessageId());
        } else if ($this->view->messageUpdateSubmitted()) {
            $id = $this->view->getUpdatedMessageId();
            $this->validateMessageUpdate($id);
        }
    }

    private function handleMessageDelete(): void
    {
        if (\View\MessageTable::userWantsToDeleteMessage()) {
            $id = \View\MessageTable::getMessageId();
            $this->validateMessageUpdate($id);
            \Model\MessageDAL::deleteMessage(\View\MessageTable::getMessageId());
            $this->view->setMessage('Your message was removed!');
        }
    }

    private function validateMessageUpdate(int $id): void
    {
        $oldMessage = \Model\MessageDAL::getMessageById($id);
        if ($oldMessage->author != $this->storage->getUsername()) {
            $this->view->setIsAuthorizedEditor(false);
            throw new \Exception('You cannot edit other people\'s messages!');
        }
    }
}
