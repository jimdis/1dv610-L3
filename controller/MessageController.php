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
        $this->view->setContentInput('');
    }

    private function handleNewMessage()
    {
        if ($this->view->newMessageSubmitted()) {
            $this->message = $this->view->getNewMessage();
            $this->validateMessageAuthor();
            \Model\MessageDAL::storeMessage($this->message);
            $this->view->setMessage('Your message was submitted!');
        }
    }


    private function validateMessageAuthor(): void
    {
        if (!$this->storage->getUserIsAuthenticated()) {
            $this->checkForExistingUser();
        } else if ($this->message->author != $this->storage->getUsername()) {
            throw new \Exception('You can only save messages under your real username');
        } else {
            $this->message->setIsVerified(true);
        }
    }

    private function checkForExistingUser(): void
    {
        if (\Model\UserDAL::doesUsernameExist($this->message->author)) {
            throw new \Exception('Username already exists. Pick another!');
        }
    }

    private function handleMessageUpdate(): void
    {
        if (\View\MessageTable::userWantsToEditMessage()) {
            $this->view->setShowEditForm(true);
            $this->view->setEditMessageId(\View\MessageTable::getMessageId());
        } else if ($this->view->messageUpdateSubmitted()) {
            $this->message = $this->view->getNewMessage();
            $this->validateMessageUpdate($this->message->id);
            \Model\MessageDAL::updateMessage($this->message);
            $this->view->setMessage('Your message was updated!');
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

    private function handleMessageDelete(): void
    {
        if (\View\MessageTable::userWantsToDeleteMessage()) {
            $id = \View\MessageTable::getMessageId();
            $this->validateMessageUpdate($id);
            \Model\MessageDAL::deleteMessage(\View\MessageTable::getMessageId());
            $this->view->setMessage('Your message was removed!');
        }
    }
}
